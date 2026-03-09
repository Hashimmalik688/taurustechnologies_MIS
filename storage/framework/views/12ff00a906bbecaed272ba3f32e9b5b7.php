<?php $__env->startSection('title', 'Create Announcement'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-wrapper">
    <div class="page-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bx bx-plus"></i> Create New Announcement
            </h2>
            <a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('admin.announcements.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">
                            <i class="bx bx-heading"></i> Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Enter announcement title (max 100 characters)"
                               maxlength="100"
                               value="<?php echo e(old('title')); ?>"
                               required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small class="text-muted d-block mt-2">Keep it short and impactful</small>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold">
                            <i class="bx bx-message-dots"></i> Message <span class="text-danger">*</span>
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Enter announcement message (max 500 characters)"
                                  rows="5"
                                  maxlength="500"
                                  required><?php echo e(old('message')); ?></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <small class="text-muted d-block mt-2">Be clear and concise with your message</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="animation" class="form-label fw-bold">
                                <i class="bx bx-play"></i> Animation <span class="text-danger">*</span>
                            </label>
                            <select id="animation" 
                                    name="animation" 
                                    class="form-select <?php $__errorArgs = ['animation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">Select an animation style</option>
                                <option value="slide" <?php echo e(old('animation') === 'slide' ? 'selected' : ''); ?>>Slide Down</option>
                                <option value="fade" <?php echo e(old('animation') === 'fade' ? 'selected' : ''); ?>>Fade In</option>
                                <option value="bounce" <?php echo e(old('animation') === 'bounce' ? 'selected' : ''); ?>>Bounce</option>
                                <option value="wave" <?php echo e(old('animation') === 'wave' ? 'selected' : ''); ?>>Wave</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['animation'];
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

                        <div class="col-md-6 mb-4">
                            <label for="community_id" class="form-label fw-bold">
                                <i class="bx bx-group"></i> Community <span class="text-muted">(optional)</span>
                            </label>
                            <select id="community_id" 
                                    name="community_id" 
                                    class="form-select <?php $__errorArgs = ['community_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- No Community --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($community->id); ?>" <?php echo e(old('community_id') == $community->id ? 'selected' : ''); ?>>
                                        <?php echo e($community->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <small class="text-muted d-block mt-2">
                                <i class="bx bx-info-circle"></i> Selecting a community will force icon to "Important" and color to "Red"
                            </small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['community_id'];
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
                    </div>
                            <label for="background_color" class="form-label fw-bold">
                                <i class="bx bx-palette"></i> Background Color <span class="text-danger">*</span>
                            </label>
                            <select id="background_color" 
                                    name="background_color" 
                                    class="form-select <?php $__errorArgs = ['background_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">Select a background color</option>
                                <option value="red" <?php echo e(old('background_color') === 'red' ? 'selected' : ''); ?>>Red</option>
                                <option value="yellow" <?php echo e(old('background_color') === 'yellow' ? 'selected' : ''); ?>>Yellow</option>
                                <option value="blue" <?php echo e(old('background_color') === 'blue' ? 'selected' : ''); ?>>Blue</option>
                                <option value="green" <?php echo e(old('background_color') === 'green' ? 'selected' : ''); ?>>Green</option>
                                <option value="purple" <?php echo e(old('background_color') === 'purple' ? 'selected' : ''); ?>>Purple</option>
                                <option value="orange" <?php echo e(old('background_color') === 'orange' ? 'selected' : ''); ?>>Orange</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['background_color'];
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
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="icon" class="form-label fw-bold">
                                <i class="bx bx-smile"></i> Icon <span class="text-danger">*</span>
                            </label>
                            <select id="icon" 
                                    name="icon" 
                                    class="form-select <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">Select an icon</option>
                                <option value="warning" <?php echo e(old('icon') === 'warning' ? 'selected' : ''); ?>>⚠️ Warning</option>
                                <option value="info" <?php echo e(old('icon') === 'info' ? 'selected' : ''); ?>>ℹ️ Info</option>
                                <option value="important" <?php echo e(old('icon') === 'important' ? 'selected' : ''); ?>>⭐ Important</option>
                                <option value="star" <?php echo e(old('icon') === 'star' ? 'selected' : ''); ?>>✭ Star</option>
                                <option value="check" <?php echo e(old('icon') === 'check' ? 'selected' : ''); ?>>✅ Check</option>
                                <option value="alert" <?php echo e(old('icon') === 'alert' ? 'selected' : ''); ?>>🔔 Alert</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['icon'];
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

                        <div class="col-md-6 mb-4">
                            <label for="auto_dismiss" class="form-label fw-bold">
                                <i class="bx bx-time"></i> Auto Dismiss <span class="text-danger">*</span>
                            </label>
                            <select id="auto_dismiss" 
                                    name="auto_dismiss" 
                                    class="form-select <?php $__errorArgs = ['auto_dismiss'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">Select dismissal behavior</option>
                                <option value="never" <?php echo e(old('auto_dismiss') === 'never' ? 'selected' : ''); ?>>Never (Sticky)</option>
                                <option value="5s" <?php echo e(old('auto_dismiss') === '5s' ? 'selected' : ''); ?>>5 Seconds</option>
                                <option value="10s" <?php echo e(old('auto_dismiss') === '10s' ? 'selected' : ''); ?>>10 Seconds</option>
                                <option value="30s" <?php echo e(old('auto_dismiss') === '30s' ? 'selected' : ''); ?>>30 Seconds</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['auto_dismiss'];
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
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="publish_now" 
                               name="publish_now"
                               value="1"
                               checked>
                        <label class="form-check-label" for="publish_now">
                            <i class="bx bx-send"></i> Publish Immediately
                        </label>
                        <small class="text-muted d-block mt-2">If unchecked, the announcement will be published in 1 hour</small>
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Preview</label>
 <div id="previewAnnouncement" class="p-3 rounded bg-surface-100" >
                            <div class="d-flex align-items-center gap-2 text-surface-500">
                                <i class="bx bx-bell"></i>
                                <div>
                                    <strong id="previewTitle">Your announcement title here</strong>
                                    <p id="previewMessage" class="mb-0 small mt-1">Your announcement message will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> Create Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .page-wrapper {
        padding: 1.5rem 0;
    }

    .page-content {
        max-width: 900px;
        margin: 0 auto;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .form-label {
        color: var(--bs-surface-800);
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-color: var(--bs-surface-200);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--bs-gold);
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const messageInput = document.getElementById('message');
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');

    // Update preview in real-time
    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Your announcement title here';
    });

    messageInput.addEventListener('input', function() {
        previewMessage.textContent = this.value || 'Your announcement message will appear here';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/announcement/create.blade.php ENDPATH**/ ?>