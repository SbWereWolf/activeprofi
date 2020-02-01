var app = (function () {
    'use strict';

    function noop() { }
    function add_location(element, file, line, column, char) {
        element.__svelte_meta = {
            loc: { file, line, column, char }
        };
    }
    function run(fn) {
        return fn();
    }
    function blank_object() {
        return Object.create(null);
    }
    function run_all(fns) {
        fns.forEach(run);
    }
    function is_function(thing) {
        return typeof thing === 'function';
    }
    function safe_not_equal(a, b) {
        return a != a ? b == b : a !== b || ((a && typeof a === 'object') || typeof a === 'function');
    }

    function append(target, node) {
        target.appendChild(node);
    }
    function insert(target, node, anchor) {
        target.insertBefore(node, anchor || null);
    }
    function detach(node) {
        node.parentNode.removeChild(node);
    }
    function element(name) {
        return document.createElement(name);
    }
    function text(data) {
        return document.createTextNode(data);
    }
    function space() {
        return text(' ');
    }
    function attr(node, attribute, value) {
        if (value == null)
            node.removeAttribute(attribute);
        else if (node.getAttribute(attribute) !== value)
            node.setAttribute(attribute, value);
    }
    function children(element) {
        return Array.from(element.childNodes);
    }
    function set_style(node, key, value, important) {
        node.style.setProperty(key, value, important ? 'important' : '');
    }
    function custom_event(type, detail) {
        const e = document.createEvent('CustomEvent');
        e.initCustomEvent(type, false, false, detail);
        return e;
    }

    let current_component;
    function set_current_component(component) {
        current_component = component;
    }

    const dirty_components = [];
    const binding_callbacks = [];
    const render_callbacks = [];
    const flush_callbacks = [];
    const resolved_promise = Promise.resolve();
    let update_scheduled = false;
    function schedule_update() {
        if (!update_scheduled) {
            update_scheduled = true;
            resolved_promise.then(flush);
        }
    }
    function add_render_callback(fn) {
        render_callbacks.push(fn);
    }
    function flush() {
        const seen_callbacks = new Set();
        do {
            // first, call beforeUpdate functions
            // and update components
            while (dirty_components.length) {
                const component = dirty_components.shift();
                set_current_component(component);
                update(component.$$);
            }
            while (binding_callbacks.length)
                binding_callbacks.pop()();
            // then, once components are updated, call
            // afterUpdate functions. This may cause
            // subsequent updates...
            for (let i = 0; i < render_callbacks.length; i += 1) {
                const callback = render_callbacks[i];
                if (!seen_callbacks.has(callback)) {
                    callback();
                    // ...so guard against infinite loops
                    seen_callbacks.add(callback);
                }
            }
            render_callbacks.length = 0;
        } while (dirty_components.length);
        while (flush_callbacks.length) {
            flush_callbacks.pop()();
        }
        update_scheduled = false;
    }
    function update($$) {
        if ($$.fragment !== null) {
            $$.update();
            run_all($$.before_update);
            const dirty = $$.dirty;
            $$.dirty = [-1];
            $$.fragment && $$.fragment.p($$.ctx, dirty);
            $$.after_update.forEach(add_render_callback);
        }
    }
    const outroing = new Set();
    function transition_in(block, local) {
        if (block && block.i) {
            outroing.delete(block);
            block.i(local);
        }
    }
    function mount_component(component, target, anchor) {
        const { fragment, on_mount, on_destroy, after_update } = component.$$;
        fragment && fragment.m(target, anchor);
        // onMount happens before the initial afterUpdate
        add_render_callback(() => {
            const new_on_destroy = on_mount.map(run).filter(is_function);
            if (on_destroy) {
                on_destroy.push(...new_on_destroy);
            }
            else {
                // Edge case - component was destroyed immediately,
                // most likely as a result of a binding initialising
                run_all(new_on_destroy);
            }
            component.$$.on_mount = [];
        });
        after_update.forEach(add_render_callback);
    }
    function destroy_component(component, detaching) {
        const $$ = component.$$;
        if ($$.fragment !== null) {
            run_all($$.on_destroy);
            $$.fragment && $$.fragment.d(detaching);
            // TODO null out other refs, including component.$$ (but need to
            // preserve final state?)
            $$.on_destroy = $$.fragment = null;
            $$.ctx = [];
        }
    }
    function make_dirty(component, i) {
        if (component.$$.dirty[0] === -1) {
            dirty_components.push(component);
            schedule_update();
            component.$$.dirty.fill(0);
        }
        component.$$.dirty[(i / 31) | 0] |= (1 << (i % 31));
    }
    function init(component, options, instance, create_fragment, not_equal, props, dirty = [-1]) {
        const parent_component = current_component;
        set_current_component(component);
        const prop_values = options.props || {};
        const $$ = component.$$ = {
            fragment: null,
            ctx: null,
            // state
            props,
            update: noop,
            not_equal,
            bound: blank_object(),
            // lifecycle
            on_mount: [],
            on_destroy: [],
            before_update: [],
            after_update: [],
            context: new Map(parent_component ? parent_component.$$.context : []),
            // everything else
            callbacks: blank_object(),
            dirty
        };
        let ready = false;
        $$.ctx = instance
            ? instance(component, prop_values, (i, ret, ...rest) => {
                const value = rest.length ? rest[0] : ret;
                if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {
                    if ($$.bound[i])
                        $$.bound[i](value);
                    if (ready)
                        make_dirty(component, i);
                }
                return ret;
            })
            : [];
        $$.update();
        ready = true;
        run_all($$.before_update);
        // `false` as a special case of no DOM component
        $$.fragment = create_fragment ? create_fragment($$.ctx) : false;
        if (options.target) {
            if (options.hydrate) {
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.l(children(options.target));
            }
            else {
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.c();
            }
            if (options.intro)
                transition_in(component.$$.fragment);
            mount_component(component, options.target, options.anchor);
            flush();
        }
        set_current_component(parent_component);
    }
    class SvelteComponent {
        $destroy() {
            destroy_component(this, 1);
            this.$destroy = noop;
        }
        $on(type, callback) {
            const callbacks = (this.$$.callbacks[type] || (this.$$.callbacks[type] = []));
            callbacks.push(callback);
            return () => {
                const index = callbacks.indexOf(callback);
                if (index !== -1)
                    callbacks.splice(index, 1);
            };
        }
        $set() {
            // overridden by instance, if it has props
        }
    }

    function dispatch_dev(type, detail) {
        document.dispatchEvent(custom_event(type, Object.assign({ version: '3.17.1' }, detail)));
    }
    function append_dev(target, node) {
        dispatch_dev("SvelteDOMInsert", { target, node });
        append(target, node);
    }
    function insert_dev(target, node, anchor) {
        dispatch_dev("SvelteDOMInsert", { target, node, anchor });
        insert(target, node, anchor);
    }
    function detach_dev(node) {
        dispatch_dev("SvelteDOMRemove", { node });
        detach(node);
    }
    function attr_dev(node, attribute, value) {
        attr(node, attribute, value);
        if (value == null)
            dispatch_dev("SvelteDOMRemoveAttribute", { node, attribute });
        else
            dispatch_dev("SvelteDOMSetAttribute", { node, attribute, value });
    }
    class SvelteComponentDev extends SvelteComponent {
        constructor(options) {
            if (!options || (!options.target && !options.$$inline)) {
                throw new Error(`'target' is a required option`);
            }
            super();
        }
        $destroy() {
            super.$destroy();
            this.$destroy = () => {
                console.warn(`Component was already destroyed`); // eslint-disable-line no-console
            };
        }
    }

    /* src\frontend\App.svelte generated by Svelte v3.17.1 */

    const file = "src\\frontend\\App.svelte";

    function create_fragment(ctx) {
    	let div14;
    	let h1;
    	let t1;
    	let form0;
    	let div1;
    	let label0;
    	let t3;
    	let div0;
    	let input0;
    	let t4;
    	let div2;
    	let t5;
    	let div3;
    	let t6;
    	let div13;
    	let div12;
    	let div11;
    	let div4;
    	let h4;
    	let span0;
    	let t7;
    	let span1;
    	let t8;
    	let button;
    	let t10;
    	let div10;
    	let form1;
    	let div5;
    	let label1;
    	let span2;
    	let t11;
    	let t12;
    	let input1;
    	let t13;
    	let div6;
    	let label2;
    	let span3;
    	let t14;
    	let t15;
    	let input2;
    	let t16;
    	let div7;
    	let label3;
    	let span4;
    	let t17;
    	let t18;
    	let input3;
    	let t19;
    	let div8;
    	let label4;
    	let span5;
    	let t20;
    	let t21;
    	let input4;
    	let t22;
    	let div9;
    	let label5;
    	let span6;
    	let t23;
    	let t24;
    	let textarea;

    	const block = {
    		c: function create() {
    			div14 = element("div");
    			h1 = element("h1");
    			h1.textContent = "Трекер рабочих заданий";
    			t1 = space();
    			form0 = element("form");
    			div1 = element("div");
    			label0 = element("label");
    			label0.textContent = "Поиск";
    			t3 = space();
    			div0 = element("div");
    			input0 = element("input");
    			t4 = space();
    			div2 = element("div");
    			t5 = space();
    			div3 = element("div");
    			t6 = space();
    			div13 = element("div");
    			div12 = element("div");
    			div11 = element("div");
    			div4 = element("div");
    			h4 = element("h4");
    			span0 = element("span");
    			t7 = text(" Информация по задаче #");
    			span1 = element("span");
    			t8 = space();
    			button = element("button");
    			button.textContent = "×";
    			t10 = space();
    			div10 = element("div");
    			form1 = element("form");
    			div5 = element("div");
    			label1 = element("label");
    			span2 = element("span");
    			t11 = text(" Заголовок");
    			t12 = space();
    			input1 = element("input");
    			t13 = space();
    			div6 = element("div");
    			label2 = element("label");
    			span3 = element("span");
    			t14 = text(" Дата выполнения");
    			t15 = space();
    			input2 = element("input");
    			t16 = space();
    			div7 = element("div");
    			label3 = element("label");
    			span4 = element("span");
    			t17 = text(" Автор");
    			t18 = space();
    			input3 = element("input");
    			t19 = space();
    			div8 = element("div");
    			label4 = element("label");
    			span5 = element("span");
    			t20 = text(" Статус");
    			t21 = space();
    			input4 = element("input");
    			t22 = space();
    			div9 = element("div");
    			label5 = element("label");
    			span6 = element("span");
    			t23 = text(" Описание");
    			t24 = space();
    			textarea = element("textarea");
    			add_location(h1, file, 24, 4, 529);
    			attr_dev(label0, "class", "control-label col-sm-2");
    			attr_dev(label0, "for", "sample");
    			add_location(label0, file, 28, 12, 654);
    			attr_dev(input0, "id", "sample");
    			attr_dev(input0, "class", "form-control ");
    			attr_dev(input0, "type", "search");
    			attr_dev(input0, "placeholder", "Введите наименование задачи");
    			input0.autofocus = true;
    			attr_dev(input0, "autocomplete", "on");
    			add_location(input0, file, 30, 16, 771);
    			attr_dev(div0, "class", "col-sm-10");
    			add_location(div0, file, 29, 12, 731);
    			attr_dev(div1, "class", "form-group");
    			add_location(div1, file, 27, 8, 617);
    			attr_dev(form0, "class", "form-horizontal");
    			attr_dev(form0, "id", "search");
    			add_location(form0, file, 26, 4, 566);
    			attr_dev(div2, "id", "taskList");
    			add_location(div2, file, 34, 4, 949);
    			attr_dev(div3, "id", "paging");
    			add_location(div3, file, 36, 4, 984);
    			attr_dev(span0, "class", "glyphicon glyphicon-list");
    			add_location(span0, file, 44, 20, 1229);
    			attr_dev(span1, "id", "id");
    			add_location(span1, file, 44, 89, 1298);
    			add_location(h4, file, 44, 16, 1225);
    			attr_dev(button, "type", "button");
    			attr_dev(button, "class", "close");
    			attr_dev(button, "data-dismiss", "modal");
    			add_location(button, file, 45, 16, 1341);
    			attr_dev(div4, "class", "modal-header");
    			set_style(div4, "padding", "35px 50px");
    			add_location(div4, file, 43, 12, 1155);
    			attr_dev(span2, "class", "glyphicon glyphicon-text-color");
    			add_location(span2, file, 50, 33, 1566);
    			attr_dev(label1, "for", "title");
    			add_location(label1, file, 50, 14, 1547);
    			attr_dev(input1, "type", "text");
    			attr_dev(input1, "class", "form-control");
    			attr_dev(input1, "id", "title");
    			attr_dev(input1, "placeholder", "Значение не задано");
    			input1.disabled = true;
    			add_location(input1, file, 51, 14, 1651);
    			attr_dev(div5, "class", "form-group");
    			add_location(div5, file, 49, 12, 1508);
    			attr_dev(span3, "class", "glyphicon glyphicon-time");
    			add_location(span3, file, 54, 32, 1833);
    			attr_dev(label2, "for", "date");
    			add_location(label2, file, 54, 14, 1815);
    			attr_dev(input2, "type", "text");
    			attr_dev(input2, "class", "form-control");
    			attr_dev(input2, "id", "date");
    			attr_dev(input2, "placeholder", "Значение не задано");
    			input2.disabled = true;
    			add_location(input2, file, 55, 14, 1918);
    			attr_dev(div6, "class", "form-group");
    			add_location(div6, file, 53, 12, 1776);
    			attr_dev(span4, "class", "glyphicon glyphicon-user");
    			add_location(span4, file, 58, 34, 2101);
    			attr_dev(label3, "for", "author");
    			add_location(label3, file, 58, 14, 2081);
    			attr_dev(input3, "type", "text");
    			attr_dev(input3, "class", "form-control");
    			attr_dev(input3, "id", "author");
    			attr_dev(input3, "placeholder", "Значение не задано");
    			input3.disabled = true;
    			add_location(input3, file, 59, 14, 2176);
    			attr_dev(div7, "class", "form-group");
    			add_location(div7, file, 57, 12, 2042);
    			attr_dev(span5, "class", "glyphicon glyphicon-check");
    			add_location(span5, file, 62, 34, 2361);
    			attr_dev(label4, "for", "status");
    			add_location(label4, file, 62, 14, 2341);
    			attr_dev(input4, "type", "text");
    			attr_dev(input4, "class", "form-control");
    			attr_dev(input4, "id", "status");
    			attr_dev(input4, "placeholder", "Значение не задано");
    			input4.disabled = true;
    			add_location(input4, file, 63, 14, 2438);
    			attr_dev(div8, "class", "form-group");
    			add_location(div8, file, 61, 12, 2302);
    			attr_dev(span6, "class", "glyphicon glyphicon-pencil");
    			add_location(span6, file, 66, 39, 2628);
    			attr_dev(label5, "for", "description");
    			add_location(label5, file, 66, 14, 2603);
    			attr_dev(textarea, "class", "form-control");
    			attr_dev(textarea, "id", "description");
    			attr_dev(textarea, "placeholder", "Значение не задано");
    			textarea.disabled = true;
    			add_location(textarea, file, 67, 14, 2708);
    			attr_dev(div9, "class", "form-group");
    			add_location(div9, file, 65, 12, 2564);
    			attr_dev(form1, "role", "form");
    			add_location(form1, file, 48, 10, 1477);
    			attr_dev(div10, "class", "modal-body");
    			add_location(div10, file, 47, 8, 1442);
    			attr_dev(div11, "class", "modal-content");
    			add_location(div11, file, 42, 8, 1115);
    			attr_dev(div12, "class", "modal-dialog");
    			add_location(div12, file, 40, 4, 1079);
    			attr_dev(div13, "class", "modal fade");
    			attr_dev(div13, "id", "detailTaskView");
    			attr_dev(div13, "role", "dialog");
    			add_location(div13, file, 39, 2, 1016);
    			attr_dev(div14, "class", "container");
    			add_location(div14, file, 23, 0, 501);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div14, anchor);
    			append_dev(div14, h1);
    			append_dev(div14, t1);
    			append_dev(div14, form0);
    			append_dev(form0, div1);
    			append_dev(div1, label0);
    			append_dev(div1, t3);
    			append_dev(div1, div0);
    			append_dev(div0, input0);
    			append_dev(div14, t4);
    			append_dev(div14, div2);
    			append_dev(div14, t5);
    			append_dev(div14, div3);
    			append_dev(div14, t6);
    			append_dev(div14, div13);
    			append_dev(div13, div12);
    			append_dev(div12, div11);
    			append_dev(div11, div4);
    			append_dev(div4, h4);
    			append_dev(h4, span0);
    			append_dev(h4, t7);
    			append_dev(h4, span1);
    			append_dev(div4, t8);
    			append_dev(div4, button);
    			append_dev(div11, t10);
    			append_dev(div11, div10);
    			append_dev(div10, form1);
    			append_dev(form1, div5);
    			append_dev(div5, label1);
    			append_dev(label1, span2);
    			append_dev(label1, t11);
    			append_dev(div5, t12);
    			append_dev(div5, input1);
    			append_dev(form1, t13);
    			append_dev(form1, div6);
    			append_dev(div6, label2);
    			append_dev(label2, span3);
    			append_dev(label2, t14);
    			append_dev(div6, t15);
    			append_dev(div6, input2);
    			append_dev(form1, t16);
    			append_dev(form1, div7);
    			append_dev(div7, label3);
    			append_dev(label3, span4);
    			append_dev(label3, t17);
    			append_dev(div7, t18);
    			append_dev(div7, input3);
    			append_dev(form1, t19);
    			append_dev(form1, div8);
    			append_dev(div8, label4);
    			append_dev(label4, span5);
    			append_dev(label4, t20);
    			append_dev(div8, t21);
    			append_dev(div8, input4);
    			append_dev(form1, t22);
    			append_dev(form1, div9);
    			append_dev(div9, label5);
    			append_dev(label5, span6);
    			append_dev(label5, t23);
    			append_dev(div9, t24);
    			append_dev(div9, textarea);
    			input0.focus();
    		},
    		p: noop,
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div14);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function loadStartScreen() {
    	loadPaging(0, settings.capacity);
    	loadPage(0, settings.capacity);
    }

    function instance($$self) {
    	jQuery(function ($) {
    		loadStartScreen();

    		$("#search").submit(async function (event) {
    			event.preventDefault();
    			const sample = $("#sample").val();

    			if (sample) {
    				search(sample);
    			}

    			if (!sample) {
    				loadStartScreen();
    			}
    		});
    	});

    	$$self.$capture_state = () => {
    		return {};
    	};

    	$$self.$inject_state = $$props => {
    		
    	};

    	return [];
    }

    class App extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance, create_fragment, safe_not_equal, {});

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "App",
    			options,
    			id: create_fragment.name
    		});
    	}
    }

    var app = new App({
    	target: document.body
    });

    return app;

}());
//# sourceMappingURL=bundle.js.map
