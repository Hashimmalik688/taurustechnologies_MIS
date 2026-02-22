<?php $__env->startSection('title', 'Confirm Password | CRM – Taurus Technologies'); ?>
<?php $__env->startSection('body-class', 'auth-body-bg'); ?>

<?php $__env->startSection('css'); ?>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --gold-bright: var(--bs-gold-bright);
  --on-gold: var(--bs-surface-700);
  --dark-bg: var(--bs-surface-900);
  --card-bg: rgba(17, 24, 39, 0.90);
}

body.auth-body-bg {
  background: var(--dark-bg) !important;
  color: var(--bs-surface-200);
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
  font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  margin: 0;
  padding: 0;
}

#vanta-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
}

.auth-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  position: relative;
  z-index: 1;
}

.auth-card {
  width: 100%;
  max-width: 480px;
  border-radius: 16px;
  background: var(--card-bg);
  border: 1px solid rgba(212, 175, 55, 0.22);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.auth-card .inner {
  padding: 2rem 1.5rem;
}

@media (min-width: 576px) {
  .auth-card .inner {
    padding: 2.5rem 2.5rem;
  }
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.icon-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: var(--bs-surface-700);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(212, 175, 55, 0.25);
  margin: 0 auto 1.5rem;
}

h1.auth-title {
  font-size: 1.65rem;
  font-weight: 700;
  line-height: 1.25;
  margin-bottom: 0.5rem;
  color: var(--bs-surface-50);
}

.tagline {
  color: var(--bs-surface-300);
  opacity: 0.95;
  margin-bottom: 0;
  font-size: 0.95rem;
  line-height: 1.5;
}

.form-label {
  margin-bottom: 0.5rem;
  color: var(--bs-surface-300);
  font-weight: 500;
  font-size: 0.9rem;
}

.form-control {
  height: 48px;
  font-size: 0.95rem;
  background: var(--bs-surface-900);
  color: var(--bs-surface-50);
  border: 1px solid var(--bs-surface-700);
  border-radius: 10px;
  transition: all 0.2s ease;
}

.form-control::placeholder {
  color: var(--bs-surface-muted);
}

.form-control:focus {
  background: var(--bs-surface-900);
  border-color: var(--bs-gold);
  box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.20);
  color: var(--bs-surface-50);
  outline: none;
}

.form-control.is-invalid {
  border-color: var(--bs-ui-danger);
}

.form-control.is-invalid:focus {
  box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.20);
}

.btn-gold {
  background: linear-gradient(90deg, var(--bs-gold), var(--bs-gold-bright));
  color: var(--bs-surface-900);
  font-weight: 700;
  border: none;
  height: 48px;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.btn-gold:hover {
  filter: brightness(1.1);
  box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
  transform: translateY(-1px);
  color: var(--bs-surface-900);
}

.btn-gold:active {
  transform: translateY(0);
}

a.link-gold {
  color: var(--bs-gold);
  text-decoration: none;
  transition: all 0.2s ease;
}

a.link-gold:hover {
  text-decoration: underline;
  color: var(--bs-gold-bright);
}

.alert {
  border-radius: 10px;
  margin-bottom: 1.5rem;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: var(--bs-ui-danger);
}

.invalid-feedback {
  display: block;
  color: var(--bs-ui-danger);
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

.security-notice {
  background: rgba(212, 175, 55, 0.08);
  border: 1px solid rgba(212, 175, 55, 0.18);
  border-radius: 10px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: start;
  gap: 0.75rem;
}

.security-notice i {
  font-size: 20px;
  color: var(--bs-gold);
  margin-top: 2px;
}

.security-notice-text {
  flex: 1;
  font-size: 0.9rem;
  color: var(--bs-surface-300);
  line-height: 1.5;
}

.auth-footer {
  padding: 1.25rem 1.5rem;
  text-align: center;
  color: var(--bs-surface-muted);
  font-size: 0.85rem;
  border-top: 1px solid rgba(212, 175, 55, 0.1);
}

.mb-3 {
  margin-bottom: 1rem !important;
}

.mb-4 {
  margin-bottom: 1.5rem !important;
}

@media (max-width: 575px) {
  h1.auth-title {
    font-size: 1.4rem;
  }
  
  .auth-card .inner {
    padding: 1.75rem 1.25rem;
  }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="vanta-bg"></div>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="inner">
      <div class="auth-header">
        <div class="icon-circle">
          <i class="bx bx-shield-quarter u-fs-32 text-gold"></i>
        </div>
        
        <h1 class="auth-title">Confirm Password</h1>
        <p class="tagline">This is a secure area. Please confirm your<br>password before continuing.</p>
      </div>

      <div class="security-notice">
        <i class="bx bx-info-circle"></i>
        <div class="security-notice-text">
          For your security, please re-enter your password to access this sensitive area.
        </div>
      </div>

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0" style="padding-left: 1.25rem;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </ul>
        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

      <form method="POST" action="<?php echo e(route('password.confirm')); ?>" novalidate>
        <?php echo csrf_field(); ?>

        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input id="password" 
                 type="password" 
                 name="password"
                 class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 placeholder="••••••••" 
                 required 
                 autofocus>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-gold w-100">
            Confirm Password
          </button>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Route::has('password.request')): ?>
          <div class="text-center">
            <a href="<?php echo e(route('password.request')); ?>" class="link-gold small">
              Forgot your password?
            </a>
          </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
      </form>
    </div>

    <div class="auth-footer">
      © <script>document.write(new Date().getFullYear())</script> Taurus Technologies. All rights reserved.
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@0.5.24/dist/vanta.waves.min.js"></script>
<script>
(function initVanta() {
  if (typeof THREE === 'undefined' || !window.VANTA || !window.VANTA.WAVES) {
    return setTimeout(initVanta, 100);
  }
  
  try {
    window.VANTA.WAVES({
      el: "#vanta-bg",
      mouseControls: true,
      touchControls: true,
      gyroControls: false,
      minHeight: 200.00,
      minWidth: 200.00,
      scale: 1.00,
      scaleMobile: 1.00,
      color: 0xd4af37,
      shininess: 50.00,
      waveHeight: 15.00,
      waveSpeed: 0.50,
      zoom: 0.85
    });
  } catch (error) {
    console.error('Vanta initialization error:', error);
  }
})();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master-without-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/auth/passwords/confirm.blade.php ENDPATH**/ ?>