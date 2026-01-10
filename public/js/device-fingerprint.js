/**
 * Device Fingerprinting for Attendance Tracking
 * Generates a unique identifier for each computer/browser combination
 */

(function() {
    'use strict';

    // Generate device fingerprint from multiple browser properties
    function generateDeviceFingerprint() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillText('fingerprint', 2, 2);
        
        const fingerprint = {
            screen: `${screen.width}x${screen.height}x${screen.colorDepth}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language,
            platform: navigator.platform,
            hardwareConcurrency: navigator.hardwareConcurrency || 'unknown',
            deviceMemory: navigator.deviceMemory || 'unknown',
            canvas: canvas.toDataURL().slice(-50),
            plugins: Array.from(navigator.plugins || []).map(p => p.name).join(',').slice(0, 50)
        };

        // Create hash from fingerprint data
        const fingerprintString = JSON.stringify(fingerprint);
        let hash = 0;
        for (let i = 0; i < fingerprintString.length; i++) {
            const char = fingerprintString.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        
        return Math.abs(hash).toString(16);
    }

    // Get or create device ID from localStorage
    function getDeviceId() {
        let deviceId = localStorage.getItem('device_id');
        if (!deviceId) {
            deviceId = 'dev_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('device_id', deviceId);
        }
        return deviceId;
    }

    // Get device name (user can set custom name)
    function getDeviceName() {
        let deviceName = localStorage.getItem('device_name');
        if (!deviceName) {
            // Auto-generate name from browser/OS info
            const ua = navigator.userAgent;
            let browser = 'Unknown';
            let os = 'Unknown';
            
            if (ua.indexOf('Chrome') > -1) browser = 'Chrome';
            else if (ua.indexOf('Firefox') > -1) browser = 'Firefox';
            else if (ua.indexOf('Safari') > -1) browser = 'Safari';
            else if (ua.indexOf('Edge') > -1) browser = 'Edge';
            
            if (ua.indexOf('Windows') > -1) os = 'Windows';
            else if (ua.indexOf('Mac') > -1) os = 'Mac';
            else if (ua.indexOf('Linux') > -1) os = 'Linux';
            
            deviceName = `${os} - ${browser} (${screen.width}x${screen.height})`;
            localStorage.setItem('device_name', deviceName);
        }
        return deviceName;
    }

    // Combine fingerprint and device ID for tracking
    const deviceFingerprint = generateDeviceFingerprint();
    const deviceId = getDeviceId();
    const deviceName = getDeviceName();

    // Store in window object for access by other scripts
    window.deviceInfo = {
        fingerprint: deviceFingerprint,
        deviceId: deviceId,
        deviceName: deviceName,
        fullFingerprint: `${deviceFingerprint}_${deviceId}`
    };

    // Auto-attach to all AJAX requests
    if (window.axios) {
        axios.interceptors.request.use(config => {
            config.headers['X-Device-Fingerprint'] = window.deviceInfo.fingerprint;
            config.headers['X-Device-ID'] = window.deviceInfo.deviceId;
            config.headers['X-Device-Name'] = window.deviceInfo.deviceName;
            return config;
        });
    }

    // Also add to jQuery AJAX if available
    if (window.jQuery) {
        $(document).ajaxSend(function(event, jqxhr, settings) {
            jqxhr.setRequestHeader('X-Device-Fingerprint', window.deviceInfo.fingerprint);
            jqxhr.setRequestHeader('X-Device-ID', window.deviceInfo.deviceId);
            jqxhr.setRequestHeader('X-Device-Name', window.deviceInfo.deviceName);
        });
    }

    console.log('Device fingerprint initialized:', window.deviceInfo.fingerprint);
})();
