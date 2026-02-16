/**
 * Permission Matrix JavaScript
 * Handles real-time permission updates via AJAX
 */

(function() {
    'use strict';

    // Initialize permission matrix functionality
    function initPermissionMatrix() {
        const permissionForm = document.getElementById('permissionForm');
        if (!permissionForm) return;

        // Add change event listeners to all radio buttons
        const radioButtons = permissionForm.querySelectorAll('.permission-radio');
        let debounceTimer;

        radioButtons.forEach(radio => {
            radio.addEventListener('change', function(e) {
                // Visual feedback
                const row = this.closest('.permission-row');
                if (row) {
                    row.style.backgroundColor = '#fffbea';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 1500);
                }

                // Optional: Enable auto-save with debouncing
                // Uncomment the code below to enable AJAX auto-save
                /*
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    autoSavePermission(this);
                }, 1000);
                */
            });
        });

        // Bulk action: Grant all permissions
        const grantAllBtn = document.getElementById('grantAllBtn');
        if (grantAllBtn) {
            grantAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Grant full access to all modules for this role?')) {
                    setAllPermissions('full');
                }
            });
        }

        // Bulk action: Revoke all permissions
        const revokeAllBtn = document.getElementById('revokeAllBtn');
        if (revokeAllBtn) {
            revokeAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Revoke all access to modules for this role?')) {
                    setAllPermissions('none');
                }
            });
        }

        // Category-level quick actions
        document.querySelectorAll('.category-quick-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const category = this.dataset.category;
                const level = this.dataset.level;
                setCategoryPermissions(category, level);
            });
        });
    }

    /**
     * Set all permissions to a specific level
     */
    function setAllPermissions(level) {
        const radioButtons = document.querySelectorAll(`.permission-radio[value="${level}"]`);
        radioButtons.forEach(radio => {
            radio.checked = true;
            const row = radio.closest('.permission-row');
            if (row) {
                row.style.backgroundColor = '#d1ecf1';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 2000);
            }
        });
    }

    /**
     * Set permissions for a specific category
     */
    function setCategoryPermissions(category, level) {
        // Find all modules in this category
        const categoryHeader = Array.from(document.querySelectorAll('.category-header'))
            .find(header => header.textContent.includes(category));
        
        if (!categoryHeader) return;

        let nextElement = categoryHeader.nextElementSibling;
        while (nextElement && !nextElement.classList.contains('category-header')) {
            if (nextElement.classList.contains('permission-row')) {
                const radio = nextElement.querySelector(`.permission-radio[value="${level}"]`);
                if (radio) {
                    radio.checked = true;
                    nextElement.style.backgroundColor = '#d1ecf1';
                    setTimeout(() => {
                        nextElement.style.backgroundColor = '';
                    }, 2000);
                }
            }
            nextElement = nextElement.nextElementSibling;
        }
    }

    /**
     * Auto-save permission via AJAX
     * (Optional feature - disabled by default)
     */
    function autoSavePermission(radioElement) {
        const form = radioElement.closest('form');
        const formAction = form.action;
        const moduleName = radioElement.name.match(/permissions\[(.*?)\]/)[1];
        const level = radioElement.value;
        
        // Determine if it's a role or user permission form
        const isRoleForm = formAction.includes('/roles/');
        const isUserForm = formAction.includes('/users/');
        
        if (!isRoleForm && !isUserForm) return;

        // Extract ID from URL
        const urlParts = formAction.split('/');
        const id = urlParts[urlParts.length - 1];

        // Show loading indicator
        showLoadingIndicator(radioElement);

        // Prepare AJAX request
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/settings/permissions/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                type: isRoleForm ? 'role' : 'user',
                id: id,
                module: moduleName,
                level: level
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessIndicator(radioElement);
            } else {
                showErrorIndicator(radioElement, data.message);
            }
        })
        .catch(error => {
            console.error('Error saving permission:', error);
            showErrorIndicator(radioElement, 'Failed to save. Please try again.');
        });
    }

    /**
     * Show loading indicator
     */
    function showLoadingIndicator(element) {
        const row = element.closest('.permission-row');
        if (row) {
            row.style.opacity = '0.6';
        }
    }

    /**
     * Show success indicator
     */
    function showSuccessIndicator(element) {
        const row = element.closest('.permission-row');
        if (row) {
            row.style.opacity = '1';
            row.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 1500);
        }
    }

    /**
     * Show error indicator
     */
    function showErrorIndicator(element, message) {
        const row = element.closest('.permission-row');
        if (row) {
            row.style.opacity = '1';
            row.style.backgroundColor = '#f8d7da';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 3000);
        }
        
        // Show error message (you can use a toast library here)
        alert(message || 'Failed to save permission. Please try again.');
    }

    /**
     * Search/filter modules
     */
    function initModuleSearch() {
        const searchInput = document.getElementById('moduleSearch');
        if (!searchInput) return;

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.permission-row');

            rows.forEach(row => {
                const moduleName = row.querySelector('.module-name')?.textContent.toLowerCase() || '';
                const moduleDesc = row.querySelector('.module-description')?.textContent.toLowerCase() || '';
                
                if (moduleName.includes(searchTerm) || moduleDesc.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Hide empty categories
            document.querySelectorAll('.category-header').forEach(header => {
                let hasVisibleRows = false;
                let nextElement = header.nextElementSibling;
                
                while (nextElement && !nextElement.classList.contains('category-header')) {
                    if (nextElement.classList.contains('permission-row') && 
                        nextElement.style.display !== 'none') {
                        hasVisibleRows = true;
                        break;
                    }
                    nextElement = nextElement.nextElementSibling;
                }

                header.style.display = hasVisibleRows ? '' : 'none';
            });
        });
    }

    /**
     * Confirm before leaving with unsaved changes
     */
    function initUnsavedChangesWarning() {
        const form = document.getElementById('permissionForm');
        if (!form) return;

        let hasChanges = false;

        form.addEventListener('change', function() {
            hasChanges = true;
        });

        form.addEventListener('submit', function() {
            hasChanges = false;
        });

        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initPermissionMatrix();
            initModuleSearch();
            initUnsavedChangesWarning();
        });
    } else {
        initPermissionMatrix();
        initModuleSearch();
        initUnsavedChangesWarning();
    }

})();
