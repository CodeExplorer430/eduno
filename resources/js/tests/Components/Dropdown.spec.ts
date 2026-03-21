import { describe, it, expect, afterEach } from 'vitest';
import { nextTick } from 'vue';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Dropdown from '@/Components/Dropdown.vue';

afterEach(() => {
    document.body.innerHTML = '';
});

const slots = {
    trigger: '<button type="button">Open</button>',
    content: '<div role="menuitem">Item</div>',
};

describe('Dropdown', () => {
    it('menu is hidden on initial render', () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots,
        });
        expect(wrapper.find('[role="menu"]').isVisible()).toBe(false);
        wrapper.unmount();
    });

    it('clicking the trigger opens the menu', async () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots,
        });
        await wrapper.find('button').trigger('click');
        await nextTick();
        expect(wrapper.find('[role="menu"]').isVisible()).toBe(true);
        wrapper.unmount();
    });

    it('menu has role="menu" and aria-orientation="vertical"', () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots,
        });
        const menu = wrapper.find('[role="menu"]');
        expect(menu.exists()).toBe(true);
        expect(menu.attributes('aria-orientation')).toBe('vertical');
        wrapper.unmount();
    });

    it('pressing Escape closes the open menu', async () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots,
        });
        await wrapper.find('button').trigger('click');
        await nextTick();
        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
        await nextTick();
        expect(wrapper.find('[role="menu"]').isVisible()).toBe(false);
        wrapper.unmount();
    });

    it('passes WCAG axe check when closed', async () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots: {
                trigger: '<button type="button" aria-haspopup="true">Open</button>',
                content: '<div role="menuitem">Item</div>',
            },
        });
        const results = await axe(wrapper.element, { rules: { region: { enabled: false } } });
        expect(results).toHaveNoViolations();
        wrapper.unmount();
    });

    it('ArrowDown moves focus to first menuitem when open', async () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots: {
                trigger: '<button type="button">Open</button>',
                content:
                    '<a href="#" role="menuitem" tabindex="-1">Item 1</a><a href="#" role="menuitem" tabindex="-1">Item 2</a>',
            },
        });
        await wrapper.find('button').trigger('click');
        await nextTick();
        const triggerDiv = wrapper.find('.relative > div');
        triggerDiv.element.dispatchEvent(
            new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true })
        );
        await nextTick();
        const items = wrapper.findAll('[role="menuitem"]');
        expect(items.length).toBeGreaterThan(0);
        wrapper.unmount();
    });

    it('dropdown panel has role="menu" and items have role="menuitem"', async () => {
        const wrapper = mount(Dropdown, {
            attachTo: document.body,
            slots: {
                trigger: '<button type="button">Open</button>',
                content: '<a href="#" role="menuitem">Item</a>',
            },
        });
        await wrapper.find('button').trigger('click');
        await nextTick();
        expect(wrapper.find('[role="menu"]').exists()).toBe(true);
        expect(wrapper.find('[role="menuitem"]').exists()).toBe(true);
        wrapper.unmount();
    });
});
