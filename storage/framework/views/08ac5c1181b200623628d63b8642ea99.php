<?php $__env->startSection('title', 'Forgot Password | CRM – Taurus Technologies'); ?>
<?php $__env->startSection('body-class', 'auth-body-bg'); ?>

<?php $__env->startSection('css'); ?>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --gold: #d4af37;
  --gold-bright: #ffd54a;
  --on-gold: #111;
  --dark-bg: #0f172a;
  --card-bg: rgba(17, 24, 39, 0.90);
}

body.auth-body-bg {
  background: var(--dark-bg) !important;
  color: #e5e7eb;
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
  max-width: 520px;
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
  background: #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(212, 175, 55, 0.25);
  margin: 0 auto 1.5rem;
}

.logo-icon {
  height: 60px;
  width: auto;
  margin-bottom: 1rem;
}

h1.auth-title {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1.25;
  margin-bottom: 0.5rem;
  color: #f9fafb;
}

.tagline {
  color: #cfd6e4;
  opacity: 0.95;
  margin-bottom: 0;
  font-size: 0.95rem;
  line-height: 1.5;
}

.form-label {
  margin-bottom: 0.5rem;
  color: #cfd6e4;
  font-weight: 500;
  font-size: 0.9rem;
}

.form-control {
  height: 48px;
  font-size: 0.95rem;
  background: #0f1625;
  color: #f9fafb;
  border: 1px solid #2f3a4d;
  border-radius: 10px;
  transition: all 0.2s ease;
}

.form-control::placeholder {
  color: #9aa3ae;
}

.form-control:focus {
  background: #0b1220;
  border-color: var(--gold);
  box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.20);
  color: #f9fafb;
  outline: none;
}

.form-control.is-invalid {
  border-color: #ef4444;
}

.form-control.is-invalid:focus {
  box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.20);
}

.btn-gold {
  background: linear-gradient(90deg, var(--gold), var(--gold-bright));
  color: var(--on-gold);
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
  color: var(--on-gold);
}

.btn-gold:active {
  transform: translateY(0);
}

a.link-gold {
  color: var(--gold);
  text-decoration: none;
  transition: all 0.2s ease;
}

a.link-gold:hover {
  text-decoration: underline;
  color: var(--gold-bright);
}

.alert {
  border-radius: 10px;
  margin-bottom: 1.5rem;
}

.alert-success {
  background: rgba(34, 197, 94, 0.1);
  border: 1px solid rgba(34, 197, 94, 0.3);
  color: #86efac;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
}

.invalid-feedback {
  display: block;
  color: #fca5a5;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

.auth-footer {
  padding: 1.25rem 1.5rem;
  text-align: center;
  color: #9ca3af;
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
    font-size: 1.5rem;
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
        <img src="<?php echo e(asset('images/icon.png')); ?>" 
             alt="Taurus Icon" 
             class="logo-icon"
             onerror="this.style.display='none'">
        
        <div class="icon-circle">
          <i class="bx bx-lock-alt" style="font-size:32px; color:var(--gold);"></i>
        </div>
        
        <h1 class="auth-title">Forgot Password?</h1>
        <p class="tagline">No worries! Enter your email and we'll send you<br>a link to reset your password.</p>
      </div>

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
        <div class="alert alert-success" role="alert">
          <i class="bx bx-check-circle me-2"></i>
          <?php echo e(session('status')); ?>

        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

      <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0" style="padding-left: 1.25rem;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          </ul>
        </div>
      <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

      <form method="POST" action="<?php echo e(route('password.email')); ?>" novalidate>
        <?php echo csrf_field(); ?>

        <div class="mb-4">
          <label for="email" class="form-label">Email Address</label>
          <input id="email" 
                 type="email" 
                 name="email" 
                 value="<?php echo e(old('email')); ?>"
                 class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 placeholder="your@email.com" 
                 required 
                 autofocus>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
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
            Send Reset Link
          </button>
        </div>

        <div class="text-center">
          <a href="<?php echo e(route('login')); ?>" class="link-gold">
            <i class="bx bx-arrow-back me-1"></i> Back to Login
          </a>
        </div>
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
<?php echo $__env->make('layouts.master-without-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>