/**
 * Centralized Password Validation Logic
 * Handles 11 security rules for password strength and validity.
 */

function validateAdvancedPassword(password, arg2 = {}, arg3 = null) {
    const commonPasswords = ['password', '12345678', 'qwerty', 'admin123', 'admin@123', 'greenmarket', 'greenmarket123'];
    
    // Handle positional arguments for backward compatibility
    let extraInfo = {};
    if (typeof arg2 === 'string') {
        extraInfo.username = arg2;
        if (typeof arg3 === 'string') {
            extraInfo.email = arg3;
        }
    } else {
        extraInfo = arg2 || {};
    }

    const isSequential = (str) => {
        if (!str || str.length < 3) return false;
        for (let i = 0; i < str.length - 2; i++) {
            const c1 = str.toLowerCase().charCodeAt(i);
            const c2 = str.toLowerCase().charCodeAt(i + 1);
            const c3 = str.toLowerCase().charCodeAt(i + 2);
            if ((c1 + 1 === c2 && c2 + 1 === c3) || (c1 - 1 === c2 && c2 - 1 === c3)) return true;
        }
        return false;
    };

    const hasPersonalInfo = (password, info) => {
        if (!password || !info) return false;
        const normalizedPass = password.toLowerCase();
        for (const [key, value] of Object.entries(info)) {
            if (value && value.length > 3 && normalizedPass.includes(value.toLowerCase())) {
                return true;
            }
        }
        return false;
    };

    const rules = {
        'length': password.length >= 8,
        'number': /[0-9]/.test(password),
        'capital': /[A-Z]/.test(password),
        'lowercase': /[a-z]/.test(password),
        'special': /[!@#$%^&*(),.?":{}|<>]/.test(password),
        'no-space': !/\s/.test(password),
        'no-repeat': !/(.)\1\1/.test(password),
        'no-sequence': !isSequential(password),
        'not-common': !commonPasswords.includes(password.toLowerCase()),
        'no-links': !/https?:\/\/[^\s]+/.test(password),
        'no-personal': !hasPersonalInfo(password, extraInfo)
    };

    // Calculate score (out of 11)
    let score = 0;
    Object.values(rules).forEach(met => { if (met) score++; });

    // Strength levels
    const levels = [
        { text: 'None', color: '#cbd5e1', percent: 0 },
        { text: 'Very Weak', color: '#ef4444', percent: 10 },
        { text: 'Weak', color: '#ef4444', percent: 20 },
        { text: 'Weak', color: '#ef4444', percent: 30 },
        { text: 'Weak', color: '#f59e0b', percent: 40 },
        { text: 'Fair', color: '#f59e0b', percent: 50 },
        { text: 'Fair', color: '#3b82f6', percent: 60 },
        { text: 'Good', color: '#3b82f6', percent: 70 },
        { text: 'Good', color: '#8b5cf6', percent: 80 },
        { text: 'Strong', color: '#10B981', percent: 90 },
        { text: 'Very Strong', color: '#10B981', percent: 100 }
    ];

    const strength = levels[Math.min(score, 10)];

    return {
        isValid: score === 11,
        allValid: score === 11,
        score: score,
        rules: rules,
        strengthText: strength.text,
        color: strength.color,
        percent: strength.percent
    };
}

/**
 * Common helper to update UI elements based on validation result
 */
function updatePasswordRuleFeedback(result, selectorPrefix = 'rule-') {
    Object.keys(result.rules).forEach(ruleId => {
        const el = document.getElementById(selectorPrefix + ruleId);
        if (el) {
            const isValid = result.rules[ruleId];
            el.className = 'rule-item ' + (isValid ? 'valid text-success' : 'invalid text-danger');
            
            const icon = el.querySelector('i');
            if (icon) {
                icon.className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
            }
        }
    });
}
