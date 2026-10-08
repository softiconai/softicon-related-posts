// Settings page values and select choices, printed by includes/block.php.
export const blockData = () => window.alrpBlockData || { defaults: {}, choices: {} };

export const toOptions = (choices = {}) => Object.keys(choices).map(value => ({ value, label: choices[value] }));

// An attribute left unset follows Settings → Related Posts.
export const getValue = (attributes, key) => undefined !== attributes[key] ? attributes[key] : blockData().defaults[key];
