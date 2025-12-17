<?php $__env->startSection('title', 'Login | CRM – Taurus Technologies'); ?>
<?php $__env->startSection('body-class', 'auth-body-bg'); ?>

<?php $__env->startSection('css'); ?>
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
  font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  margin: 0;
  padding: 0;
}

/* Adobe-style artistic background - Static geometric patterns for speed */
#vanta-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
  background:
    linear-gradient(rgba(212, 175, 55, 0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(212, 175, 55, 0.06) 1px, transparent 1px),
    linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
  background-size: 60px 60px, 60px 60px, 100% 100%;
  background-position: 0 0, 0 0, 0 0;
  overflow: hidden;
}

/* Geometric artwork - Pure CSS, zero downloads */
#vanta-bg::before,
#vanta-bg::after {
  content: '';
  position: absolute;
  border-radius: 50%;
}

/* Large gold circle - top right */
#vanta-bg::before {
  width: 800px;
  height: 800px;
  background: radial-gradient(circle, rgba(255, 213, 74, 0.25) 0%, rgba(212, 175, 55, 0.08) 40%, transparent 70%);
  top: -400px;
  right: -300px;
}

/* Medium circle - bottom left */
#vanta-bg::after {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.20) 0%, rgba(212, 175, 55, 0.05) 40%, transparent 70%);
  bottom: -250px;
  left: -200px;
}

/* Decorative lines */
.bg-lines {
  position: fixed;
  inset: 0;
  z-index: 0;
  pointer-events: none;
}

.bg-line {
  position: absolute;
  background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.35), transparent);
  height: 3px;
  box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
}

.bg-line:nth-child(1) { top: 20%; width: 40%; left: 10%; }
.bg-line:nth-child(2) { top: 45%; width: 30%; right: 15%; }
.bg-line:nth-child(3) { top: 70%; width: 35%; left: 20%; }
.bg-line:nth-child(4) { top: 85%; width: 25%; right: 25%; }

/* Floating geometric shapes */
.bg-shape {
  position: fixed;
  border: 3px solid rgba(212, 175, 55, 0.30);
  z-index: 1;
  pointer-events: none;
  box-shadow: 0 0 20px rgba(212, 175, 55, 0.15);
}

.bg-shape.circle {
  border-radius: 50%;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
}

.bg-shape.square {
  border-radius: 8px;
  transform: rotate(45deg);
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.06) 0%, transparent 100%);
}

/* Position shapes */
.shape-1 { width: 120px; height: 120px; top: 15%; left: 8%; }
.shape-2 { width: 80px; height: 80px; top: 60%; right: 12%; }
.shape-3 { width: 60px; height: 60px; top: 35%; right: 8%; }
.shape-4 { width: 90px; height: 90px; bottom: 15%; left: 15%; }
.shape-5 { width: 100px; height: 100px; top: 75%; right: 18%; }

/* Centering wrapper */
.auth-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  position: relative;
  z-index: 10;
}

/* Glass card */
.auth-card {
  width: 100%;
  max-width: 520px;
  border-radius: 16px;
  background: var(--card-bg);
  border: 1px solid rgba(212, 175, 55, 0.22);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
}

.auth-card .inner {
  padding: 2rem 1.5rem;
}

@media (min-width: 576px) {
  .auth-card .inner {
    padding: 2.5rem 2.5rem;
  }
}

/* Header */
.auth-header {
  text-align: center;
  margin-bottom: 2rem;
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
  color: #e6e9ef;
  opacity: 0.9;
  margin-bottom: 0;
  font-size: 0.95rem;
}

/* Form Elements */
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

/* Checkbox */
.form-check-input {
  width: 1.1rem;
  height: 1.1rem;
  border: 1px solid #2f3a4d;
  background-color: #0f1625;
  cursor: pointer;
}

.form-check-input:checked {
  background-color: var(--gold);
  border-color: var(--gold);
}

.form-check-input:focus {
  border-color: var(--gold);
  box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.20);
}

.form-check-label {
  color: #cfd6e4;
  font-size: 0.9rem;
  cursor: pointer;
}

/* Button */
.btn-gold {
  background: linear-gradient(90deg, var(--gold), var(--gold-bright));
  color: var(--on-gold);
  font-weight: 700;
  border: none;
  height: 48px;
  border-radius: 10px;
  font-size: 1rem;
  cursor: pointer;
}

.btn-gold:hover {
  filter: brightness(1.1);
  color: var(--on-gold);
}

/* Links */
a.link-gold {
  color: var(--gold);
  text-decoration: none;
}

a.link-gold:hover {
  text-decoration: underline;
}

/* Alert */
.alert {
  border-radius: 10px;
  margin-bottom: 1.5rem;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
}

.alert ul {
  padding-left: 1.25rem;
  margin-bottom: 0;
}

/* Feedback */
.invalid-feedback {
  display: block;
  color: #fca5a5;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

/* Footer */
.auth-footer {
  padding: 1.25rem 1.5rem;
  text-align: center;
  color: #9ca3af;
  font-size: 0.85rem;
  border-top: 1px solid rgba(212, 175, 55, 0.1);
}

/* Spacing utilities */
.mb-3 {
  margin-bottom: 1rem !important;
}

.mb-4 {
  margin-bottom: 1.5rem !important;
}

.mt-3 {
  margin-top: 1rem !important;
}

.mt-4 {
  margin-top: 1.5rem !important;
}

/* Responsive */
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

<!-- Adobe-style decorative elements -->
<div class="bg-lines">
  <div class="bg-line"></div>
  <div class="bg-line"></div>
  <div class="bg-line"></div>
  <div class="bg-line"></div>
</div>

<!-- Floating geometric shapes -->
<div class="bg-shape circle shape-1"></div>
<div class="bg-shape square shape-2"></div>
<div class="bg-shape circle shape-3"></div>
<div class="bg-shape square shape-4"></div>
<div class="bg-shape circle shape-5"></div>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="inner">
      <div class="auth-header">
        
        <img src="<?php echo e(asset('images/icon.png')); ?>" 
             alt="Taurus Icon" 
             class="logo-icon"
             onerror="this.style.display='none'">
        <h1 class="auth-title">Welcome Back!</h1>
        <p class="tagline">Sign in to continue to Taurus CRM</p>
      </div>

      <?php if($errors->any()): ?>
        <div class="alert alert-danger" role="alert">
          <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('login')); ?>" novalidate>
        <?php echo csrf_field(); ?>

        <div class="mb-3">
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
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-3">
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
                 required>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="form-check">
            <input class="form-check-input" 
                   type="checkbox" 
                   id="remember" 
                   name="remember" 
                   <?php echo e(old('remember') ? 'checked' : ''); ?>>
            <label class="form-check-label" for="remember">
              Remember me
            </label>
          </div>
          <?php if(Route::has('password.request')): ?>
            <a class="small link-gold" href="<?php echo e(route('password.request')); ?>">
              Forgot password?
            </a>
          <?php endif; ?>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-gold w-100">
            Log In
          </button>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master-without-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/auth/login.blade.php ENDPATH**/ ?>