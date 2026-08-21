import PersistentStorage from '@typo3/backend/storage/persistent.js';

const navigationGroups = [
  {
    identifier: 'viewmend_email',
    title: 'Email',
    modules: ['viewmend_auto_replies', 'viewmend_mailings', 'viewmend_inboxmend'],
  },
];

const menuSelector = '[data-modulemenu]';

const collapsedGroups = () => {
  if (!PersistentStorage.isset('modulemenu')) {
    return {};
  }

  try {
    return JSON.parse(PersistentStorage.get('modulemenu')) || {};
  } catch (_error) {
    return {};
  }
};

const groupMarkup = (group, expanded) => {
  const item = document.createElement('li');
  item.className = `modulemenu-group vm-modulemenu-group modulemenu-group-${expanded ? 'expanded' : 'collapsed'}`;
  item.dataset.modulemenuLevel = '2';
  item.setAttribute('role', 'presentation');

  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'modulemenu-action';
  button.dataset.modulemenuIdentifier = group.identifier;
  button.dataset.modulemenuCollapsible = 'true';
  button.setAttribute('role', 'menuitem');
  button.setAttribute('title', group.title);
  button.setAttribute('tabindex', '-1');
  button.setAttribute('aria-controls', `modulemenu-group-${group.identifier}`);
  button.setAttribute('aria-haspopup', 'menu');
  button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  button.innerHTML = `
    <span class="modulemenu-icon vm-modulemenu-group__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none">
        <rect x="3.25" y="5.25" width="17.5" height="13.5" rx="2.25" stroke="currentColor" stroke-width="1.75"/>
        <path d="m4.5 7 7.5 5.25L19.5 7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </span>
    <span class="modulemenu-name">${group.title}</span>
    <span class="modulemenu-indicator" aria-hidden="true"></span>
  `;

  const list = document.createElement('ul');
  list.id = `modulemenu-group-${group.identifier}`;
  list.className = `modulemenu-group-container collapse${expanded ? ' show' : ''}`;
  list.setAttribute('role', 'menu');
  list.setAttribute('aria-orientation', 'vertical');
  list.setAttribute('aria-label', group.title);

  item.append(button, list);

  return { item, button, list };
};

const syncActiveState = (item, button, list) => {
  const active = list.querySelector('.modulemenu-action-active') !== null;
  button.classList.toggle('modulemenu-action-active', active);
  item.classList.toggle('vm-modulemenu-group--active', active);
};

const enhanceGroup = (menu, group, collapsed) => {
  const existing = menu.querySelector(`[data-viewmend-navigation-group="${group.identifier}"]`);
  if (existing) {
    const button = existing.querySelector(':scope > .modulemenu-action');
    const list = existing.querySelector(':scope > .modulemenu-group-container');
    if (button && list) {
      syncActiveState(existing, button, list);
    }
    return;
  }

  const moduleItems = group.modules
    .map((identifier) => menu.querySelector(`[data-modulemenu-identifier="${identifier}"]`)?.closest('li'))
    .filter((item) => item instanceof HTMLLIElement);

  if (moduleItems.length === 0) {
    return;
  }

  const parentList = moduleItems[0].parentElement;
  if (!(parentList instanceof HTMLUListElement) || moduleItems.some((item) => item.parentElement !== parentList)) {
    return;
  }

  const expanded = collapsed[group.identifier] !== true;
  const { item, button, list } = groupMarkup(group, expanded);
  item.dataset.viewmendNavigationGroup = group.identifier;
  parentList.append(item);

  moduleItems.forEach((moduleItem) => {
    moduleItem.dataset.modulemenuLevel = '3';
    list.append(moduleItem);
  });

  syncActiveState(item, button, list);
};

const enhanceMenu = () => {
  const menu = document.querySelector(menuSelector);
  if (!menu) {
    return;
  }

  const collapsed = collapsedGroups();
  navigationGroups.forEach((group) => enhanceGroup(menu, group, collapsed));
};

let enhancementQueued = false;
const queueEnhancement = () => {
  if (enhancementQueued) {
    return;
  }

  enhancementQueued = true;
  queueMicrotask(() => {
    enhancementQueued = false;
    enhanceMenu();
  });
};

const observer = new MutationObserver(queueEnhancement);
observer.observe(document.body, { childList: true, subtree: true });

document.addEventListener('typo3-module-load', queueEnhancement);
document.addEventListener('typo3-module-loaded', queueEnhancement);

queueEnhancement();
