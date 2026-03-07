/**
 * T009 - Local Storage Manager (SOLID Refactor)
 * Lola's Kusina - Customer Web Module
 * Author: Jullian Anjelo C. Vidal
 *
 * SOLID Principles Applied:
 *  S - Single Responsibility : Each class does ONE thing only
 *  O - Open/Closed           : Add new collectors without modifying existing ones
 *  L - Liskov Substitution   : Any IFormCollector can replace another
 *  I - Interface Segregation : Small focused "interfaces" (collector, storage, notifier)
 *  D - Dependency Inversion  : AutoSaveManager depends on abstractions, not concretions
 */

'use strict';

// =============================================================================
// S + I — StorageService
// Responsibility: ONLY reads/writes to localStorage. Nothing else.
// =============================================================================
class StorageService {
  constructor(key) {
    this._key = key;
  }

  save(data) {
    try {
      localStorage.setItem(this._key, JSON.stringify(data));
      return true;
    } catch (e) {
      console.warn('[StorageService] Save failed:', e);
      return false;
    }
  }

  load() {
    try {
      const raw = localStorage.getItem(this._key);
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      console.warn('[StorageService] Load failed:', e);
      return null;
    }
  }

  clear() {
    try {
      localStorage.removeItem(this._key);
    } catch (e) {
      console.warn('[StorageService] Clear failed:', e);
    }
  }
}


// =============================================================================
// S + I — ToastNotifier
// Responsibility: ONLY shows a toast notification. Nothing else.
// =============================================================================
class ToastNotifier {
  constructor({ id = 'lk-lsm-toast', bottom = '80px', right = '16px' } = {}) {
    this._id     = id;
    this._bottom = bottom;
    this._right  = right;
  }

  notify(message) {
    let t = document.getElementById(this._id);
    if (!t) {
      t = document.createElement('div');
      t.id = this._id;
      Object.assign(t.style, {
        position     : 'fixed',
        bottom       : this._bottom,
        right        : this._right,
        background   : '#16a34a',
        color        : '#fff',
        padding      : '7px 16px',
        borderRadius : '10px',
        fontSize     : '12px',
        fontFamily   : 'sans-serif',
        fontWeight   : '600',
        boxShadow    : '0 4px 12px rgba(0,0,0,.18)',
        opacity      : '0',
        transition   : 'opacity .3s ease',
        zIndex       : '99999',
        pointerEvents: 'none',
      });
      document.body.appendChild(t);
    }
    t.textContent = message;
    t.style.opacity = '1';
    setTimeout(() => { t.style.opacity = '0'; }, 2500);
  }
}


// =============================================================================
// S + O + L — Form Field Collectors
// Responsibility: ONLY collect a specific group of fields.
// Open/Closed   : Add new collectors (e.g. PromoCodeCollector) without
//                 touching any existing class.
// Liskov        : All collectors follow the same { collect(), restore() } shape.
// =============================================================================

/** Collector for Step 1 — delivery method */
class MethodCollector {
  collect() {
    return { orderMethod: window.orderMethod ?? null };
  }

  restore(data) {
    if (!data.orderMethod) return;
    window.orderMethod = data.orderMethod;
    document.querySelectorAll('.option-card').forEach(card => {
      card.classList.toggle('selected', card.dataset.method === data.orderMethod);
    });
  }
}

/** Collector for Step 2 — delivery address & schedule */
class DeliveryCollector {
  collect() {
    return {
      deliveryAddress : this._val('deliveryAddress'),
      customDate      : this._val('customDate'),
      customTime      : this._val('customTime'),
      riderNote       : this._val('riderNote'),
    };
  }

  restore(data) {
    this._set('deliveryAddress', data.deliveryAddress);
    this._set('customDate',      data.customDate);
    this._set('customTime',      data.customTime);
    this._set('riderNote',       data.riderNote);
  }

  _val(id) { const el = document.getElementById(id); return el ? el.value : null; }
  _set(id, val) { const el = document.getElementById(id); if (el && val != null) el.value = val; }
}

/** Collector for Step 3 — contact info */
class ContactCollector {
  collect() {
    return {
      contactName  : this._val('contactName'),
      contactPhone : this._val('contactPhone'),
    };
  }

  restore(data) {
    this._set('contactName',  data.contactName);
    this._set('contactPhone', data.contactPhone);
  }

  _val(id) { const el = document.getElementById(id); return el ? el.value : null; }
  _set(id, val) { const el = document.getElementById(id); if (el && val != null) el.value = val; }
}

/** Collector for Cart items */
class CartCollector {
  collect() {
    return { cart: window.lkCart ?? [] };
  }

  restore(data) {
    if (Array.isArray(data.cart) && data.cart.length > 0) {
      window.lkCart = data.cart;
      console.log('[CartCollector] Cart restored:', data.cart.length, 'item(s)');
    }
  }
}

/** Collector for current checkout step */
class StepCollector {
  collect() {
    return { currentStep: window.currentStep ?? 1 };
  }

  restore(data) {
    if (data.currentStep && data.currentStep > 1) {
      setTimeout(() => {
        if (typeof window.goToStep === 'function') {
          window.goToStep(data.currentStep);
        }
      }, 100);
    }
  }
}


// =============================================================================
// S — DraftSerializer
// Responsibility: ONLY merges all collectors into one snapshot and splits it back.
// =============================================================================
class DraftSerializer {
  constructor(collectors) {
    this._collectors = collectors; // Array of collector instances
  }

  serialize() {
    return this._collectors.reduce((snapshot, collector) => {
      return { ...snapshot, ...collector.collect() };
    }, { savedAt: new Date().toISOString() });
  }

  deserialize(snapshot) {
    this._collectors.forEach(collector => collector.restore(snapshot));
  }
}


// =============================================================================
// S — TimerService
// Responsibility: ONLY manages setInterval/clearInterval. Nothing else.
// =============================================================================
class TimerService {
  constructor(intervalMs) {
    this._interval = intervalMs;
    this._timer    = null;
  }

  start(callback) {
    this.stop();
    this._timer = setInterval(callback, this._interval);
    console.log(`[TimerService] Started (every ${this._interval / 1000}s).`);
  }

  stop() {
    if (this._timer) { clearInterval(this._timer); this._timer = null; }
  }
}


// =============================================================================
// D — AutoSaveManager (Orchestrator)
// Responsibility: Coordinates all services. Depends on ABSTRACTIONS (injected).
// Dependency Inversion: Does not instantiate StorageService/Notifier itself —
//                       they are injected, making this easily testable/swappable.
// =============================================================================
class AutoSaveManager {
  /**
   * @param {StorageService}  storage    - handles localStorage
   * @param {DraftSerializer} serializer - handles collect/restore
   * @param {TimerService}    timer      - handles intervals
   * @param {ToastNotifier}   notifier   - handles UI feedback
   */
  constructor(storage, serializer, timer, notifier) {
    this._storage    = storage;
    this._serializer = serializer;
    this._timer      = timer;
    this._notifier   = notifier;
  }

  /** Save current state to localStorage */
  save() {
    if (!document.getElementById('step1')) return; // Only active on cart.php
    const snapshot = this._serializer.serialize();
    if (this._storage.save(snapshot)) {
      this._notifier.notify('✔ Draft auto-saved');
      console.log('[AutoSaveManager] Saved at', snapshot.savedAt);
    }
  }

  /** Restore saved state from localStorage */
  restore() {
    if (!document.getElementById('step1')) return;
    const snapshot = this._storage.load();
    if (!snapshot) return;
    this._serializer.deserialize(snapshot);
    console.log('[AutoSaveManager] Restored from', snapshot.savedAt);
  }

  /** Clear saved draft */
  clear() {
    this._storage.clear();
    console.log('[AutoSaveManager] Draft cleared.');
  }

  /** Initialize: restore → start timer → patch placeOrder */
  init() {
    this.restore();
    this._timer.start(() => this.save());
    this._patchPlaceOrder();
  }

  /** Patch cart.php placeOrder() to clear draft on order submission */
  _patchPlaceOrder() {
    const _orig = window.placeOrder;
    if (typeof _orig !== 'function') return;
    const self = this;
    window.placeOrder = function () {
      self._timer.stop();
      self.clear();
      _orig.apply(this, arguments);
    };
  }
}


// =============================================================================
// BOOTSTRAP — Wire everything together and initialize
// =============================================================================
document.addEventListener('DOMContentLoaded', () => {

  // 1. Storage — where the draft lives
  const storage = new StorageService('lolasKusina_orderDraft');

  // 2. Collectors — O: add new ones here without changing anything else
  const serializer = new DraftSerializer([
    new MethodCollector(),
    new DeliveryCollector(),
    new ContactCollector(),
    new CartCollector(),
    new StepCollector(),
  ]);

  // 3. Timer — 20 seconds as required by T009
  const timer = new TimerService(20000);

  // 4. Notifier — visual feedback
  const notifier = new ToastNotifier();

  // 5. D: Inject all dependencies into AutoSaveManager
  const manager = new AutoSaveManager(storage, serializer, timer, notifier);

  // 6. Go!
  manager.init();

  // Expose globally if other scripts need to interact
  window.LocalStorageManager = manager;
});