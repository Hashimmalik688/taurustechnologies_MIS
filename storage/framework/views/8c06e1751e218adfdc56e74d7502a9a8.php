<?php $__env->startSection('title'); ?>
    System Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Settings
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            System Configuration
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Current IP Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary-subtle border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-primary">Your IP Address Information</h6>
                            <p class="mb-1">
                                <strong>Current IP:</strong> <code id="current-ip"><?php echo e(request()->ip()); ?></code>
                                <span id="ip-type-badge" class="badge badge-soft-warning ms-2">Checking...</span>
                            </p>
                            <p class="mb-0 text-muted">
                                <span id="network-status">Checking network status...</span>
                            </p>
                            <div id="ip-details" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <div id="all-ips"></div>
                                </small>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm me-2" onclick="testNetworkConnection()">
                                <i class="mdi mdi-refresh"></i> Test Network
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleIpDetails()">
                                <i class="mdi mdi-information-outline"></i> Details
                            </button>
                        </div>
                    </div>

                    <!-- Help Section for Localhost -->
                    <div id="localhost-help" class="alert alert-warning mt-3" style="display: none;">
                        <h6><i class="mdi mdi-alert-triangle"></i> Development Environment Detected</h6>
                        <p class="mb-2">You're running on localhost. To get your real office IP:</p>
                        <ul class="mb-2">
                            <li><strong>Option 1:</strong> Visit <a href="https://whatismyipaddress.com"
                                    target="_blank">whatismyipaddress.com</a> from your office</li>
                            <li><strong>Option 2:</strong> Deploy your app to the server and test from there</li>
                            <li><strong>Option 3:</strong> For testing only, add <code>127.0.0.1</code> to allowed networks
                            </li>
                        </ul>
                        <button class="btn btn-warning btn-sm" onclick="addLocalhostToSettings()">
                            <i class="mdi mdi-plus"></i> Add 127.0.0.1 for Testing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('settings.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-<?php echo e($group === 'attendance' ? 'account-clock' : 'cog'); ?> me-2"></i>
                                <?php echo e(ucwords(str_replace('_', ' ', $group))); ?> Settings
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-3">
                                        <label for="<?php echo e($setting->key); ?>" class="form-label">
                                            <?php echo e(ucwords(str_replace('_', ' ', str_replace($group . '_', '', $setting->key)))); ?>

                                            <?php if($setting->description): ?>
                                                <i class="mdi mdi-information-outline text-muted" data-bs-toggle="tooltip"
                                                    title="<?php echo e($setting->description); ?>"></i>
                                            <?php endif; ?>
                                        </label>

                                        <?php if($setting->type === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="<?php echo e($setting->key); ?>"
                                                    name="settings[<?php echo e($setting->key); ?>]"
                                                    <?php echo e($setting->value === 'true' ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="<?php echo e($setting->key); ?>">
                                                    <?php echo e($setting->value === 'true' ? 'Enabled' : 'Disabled'); ?>

                                                </label>
                                            </div>
                                        <?php elseif($setting->type === 'array' && $setting->key === 'office_networks'): ?>
                                            <div id="network-inputs">
                                                <?php
                                                    $networks = is_string($setting->value)
                                                        ? explode(',', $setting->value)
                                                        : [$setting->value];
                                                ?>
                                                <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="input-group mb-2 network-input-group">
                                                        <input type="text" class="form-control"
                                                            name="settings[<?php echo e($setting->key); ?>][]"
                                                            value="<?php echo e(trim($network)); ?>" placeholder="192.168.1.0/24">
                                                        <?php if($index > 0): ?>
                                                            <button class="btn btn-outline-danger" type="button"
                                                                onclick="removeNetwork(this)">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="addNetwork()">
                                                <i class="mdi mdi-plus"></i> Add Network
                                            </button>
                                            <div class="form-text">
                                                Enter IP addresses or CIDR ranges (e.g., 192.168.1.0/24, 10.0.0.100)
                                            </div>
                                        <?php elseif($setting->type === 'integer'): ?>
                                            <input type="number" class="form-control" id="<?php echo e($setting->key); ?>"
                                                name="settings[<?php echo e($setting->key); ?>]" value="<?php echo e($setting->value); ?>">
                                        <?php else: ?>
                                            <input type="<?php echo e($setting->key === 'office_start_time' ? 'time' : 'text'); ?>"
                                                class="form-control" id="<?php echo e($setting->key); ?>"
                                                name="settings[<?php echo e($setting->key); ?>]" value="<?php echo e($setting->value); ?>">
                                        <?php endif; ?>

                                        <?php if($setting->description): ?>
                                            <div class="form-text"><?php echo e($setting->description); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="row">
            <div class="col-12">
                <div class="text-end">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                        <i class="mdi mdi-refresh"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Test network connection
        function testNetworkConnection() {
            const statusElement = document.getElementById('network-status');
            const ipElement = document.getElementById('current-ip');
            const badgeElement = document.getElementById('ip-type-badge');

            statusElement.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Testing...';
            badgeElement.textContent = 'Checking...';
            badgeElement.className = 'badge badge-soft-warning ms-2';

            fetch('<?php echo e(route('settings.test-network')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update current IP display
                    ipElement.textContent = data.current_ip;

                    // Update status
                    if (data.is_in_office_network) {
                        statusElement.innerHTML =
                            `<i class="mdi mdi-check-circle text-success"></i> In Office Network (${data.matched_network})`;
                        badgeElement.textContent = 'Office Network';
                        badgeElement.className = 'badge badge-soft-success ms-2';
                    } else {
                        statusElement.innerHTML =
                            '<i class="mdi mdi-close-circle text-danger"></i> Outside Office Network';
                        badgeElement.textContent = data.is_localhost ? 'Localhost' : 'External';
                        badgeElement.className = data.is_localhost ? 'badge badge-soft-warning ms-2' :
                            'badge badge-soft-danger ms-2';
                    }

                    // Show localhost help if needed
                    const localhostHelp = document.getElementById('localhost-help');
                    if (data.is_localhost) {
                        localhostHelp.style.display = 'block';
                    } else {
                        localhostHelp.style.display = 'none';
                    }

                    // Update details section
                    const allIpsDiv = document.getElementById('all-ips');
                    let detailsHtml = '<strong>All Detected IPs:</strong><br>';
                    for (const [key, value] of Object.entries(data.all_detected_ips)) {
                        if (value) {
                            detailsHtml += `${key}: <code>${value}</code><br>`;
                        }
                    }
                    detailsHtml +=
                        `<strong>Configured Networks:</strong> <code>${data.configured_networks.join(', ')}</code>`;
                    allIpsDiv.innerHTML = detailsHtml;

                    console.log('Network Test Result:', data);
                })
                .catch(error => {
                    statusElement.innerHTML = '<i class="mdi mdi-alert-circle text-warning"></i> Test Failed';
                    badgeElement.textContent = 'Error';
                    badgeElement.className = 'badge badge-soft-danger ms-2';
                    console.error('Network test error:', error);
                });
        }

        // Toggle IP details visibility
        function toggleIpDetails() {
            const details = document.getElementById('ip-details');
            if (details.style.display === 'none') {
                details.style.display = 'block';
            } else {
                details.style.display = 'none';
            }
        }

        // Add localhost to settings for testing
        function addLocalhostToSettings() {
            const networkInputs = document.querySelectorAll('input[name="settings[office_networks][]"]');
            let hasLocalhost = false;

            networkInputs.forEach(input => {
                if (input.value.trim() === '127.0.0.1') {
                    hasLocalhost = true;
                }
            });

            if (!hasLocalhost) {
                addNetwork();
                const newInputs = document.querySelectorAll('input[name="settings[office_networks][]"]');
                const lastInput = newInputs[newInputs.length - 1];
                lastInput.value = '127.0.0.1';

                alert('Added 127.0.0.1 to office networks. Remember to save settings!');
            } else {
                alert('127.0.0.1 is already in your office networks.');
            }
        }

        // Add network input
        function addNetwork() {
            const container = document.getElementById('network-inputs');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 network-input-group';
            div.innerHTML = `
                <input type="text" 
                       class="form-control" 
                       name="settings[office_networks][]" 
                       placeholder="192.168.1.0/24">
                <button class="btn btn-outline-danger" type="button" onclick="removeNetwork(this)">
                    <i class="mdi mdi-delete"></i>
                </button>
            `;
            container.appendChild(div);
        }

        // Remove network input
        function removeNetwork(button) {
            button.closest('.network-input-group').remove();
        }

        // Reset form
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                location.reload();
            }
        }

        // Test network on page load
        document.addEventListener('DOMContentLoaded', function() {
            testNetworkConnection();
        });

        // Handle boolean toggle labels
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const label = this.nextElementSibling;
                label.textContent = this.checked ? 'Enabled' : 'Disabled';
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\code\taurus-crm-master\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>