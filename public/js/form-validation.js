/**
 * Centralized Password Validation Logic
 * Enforces the six requested password requirements.
 */

function validateAdvancedPassword(password, arg2 = {}, arg3 = null) {
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

    const rules = {
        'length': password.length >= 8,
        'number': /[0-9]/.test(password),
        'capital': /[A-Z]/.test(password),
        'lowercase': /[a-z]/.test(password),
        'special': /[!@#$%^&*()\-_+=]/.test(password),
        'no-space': !/\s/.test(password)
    };

    let score = 0;
    Object.values(rules).forEach(met => { if (met) score++; });

    const levels = [
        { text: 'None', color: '#cbd5e1', percent: 0 },
        { text: 'Weak', color: '#ef4444', percent: 20 },
        { text: 'Fair', color: '#f59e0b', percent: 40 },
        { text: 'Good', color: '#3b82f6', percent: 70 },
        { text: 'Strong', color: '#10B981', percent: 100 }
    ];

    const strength = levels[Math.min(score, 4)];

    return {
        isValid: score === 6,
        allValid: score === 6,
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
