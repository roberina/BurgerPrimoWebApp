import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AddonItemController::index
* @see app/Http/Controllers/Admin/AddonItemController.php:12
* @route '/admin/addons'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/addons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AddonItemController::index
* @see app/Http/Controllers/Admin/AddonItemController.php:12
* @route '/admin/addons'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AddonItemController::index
* @see app/Http/Controllers/Admin/AddonItemController.php:12
* @route '/admin/addons'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AddonItemController::index
* @see app/Http/Controllers/Admin/AddonItemController.php:12
* @route '/admin/addons'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AddonItemController::store
* @see app/Http/Controllers/Admin/AddonItemController.php:26
* @route '/admin/addons'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/addons',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AddonItemController::store
* @see app/Http/Controllers/Admin/AddonItemController.php:26
* @route '/admin/addons'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AddonItemController::store
* @see app/Http/Controllers/Admin/AddonItemController.php:26
* @route '/admin/addons'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AddonItemController::update
* @see app/Http/Controllers/Admin/AddonItemController.php:42
* @route '/admin/addons/{addonItem}'
*/
export const update = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/addons/{addonItem}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AddonItemController::update
* @see app/Http/Controllers/Admin/AddonItemController.php:42
* @route '/admin/addons/{addonItem}'
*/
update.url = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { addonItem: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { addonItem: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            addonItem: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        addonItem: typeof args.addonItem === 'object'
        ? args.addonItem.id
        : args.addonItem,
    }

    return update.definition.url
            .replace('{addonItem}', parsedArgs.addonItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AddonItemController::update
* @see app/Http/Controllers/Admin/AddonItemController.php:42
* @route '/admin/addons/{addonItem}'
*/
update.put = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AddonItemController::destroy
* @see app/Http/Controllers/Admin/AddonItemController.php:58
* @route '/admin/addons/{addonItem}'
*/
export const destroy = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/addons/{addonItem}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AddonItemController::destroy
* @see app/Http/Controllers/Admin/AddonItemController.php:58
* @route '/admin/addons/{addonItem}'
*/
destroy.url = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { addonItem: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { addonItem: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            addonItem: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        addonItem: typeof args.addonItem === 'object'
        ? args.addonItem.id
        : args.addonItem,
    }

    return destroy.definition.url
            .replace('{addonItem}', parsedArgs.addonItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AddonItemController::destroy
* @see app/Http/Controllers/Admin/AddonItemController.php:58
* @route '/admin/addons/{addonItem}'
*/
destroy.delete = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AddonItemController::toggle
* @see app/Http/Controllers/Admin/AddonItemController.php:65
* @route '/admin/addons/{addonItem}/toggle'
*/
export const toggle = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/admin/addons/{addonItem}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AddonItemController::toggle
* @see app/Http/Controllers/Admin/AddonItemController.php:65
* @route '/admin/addons/{addonItem}/toggle'
*/
toggle.url = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { addonItem: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { addonItem: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            addonItem: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        addonItem: typeof args.addonItem === 'object'
        ? args.addonItem.id
        : args.addonItem,
    }

    return toggle.definition.url
            .replace('{addonItem}', parsedArgs.addonItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AddonItemController::toggle
* @see app/Http/Controllers/Admin/AddonItemController.php:65
* @route '/admin/addons/{addonItem}/toggle'
*/
toggle.post = (args: { addonItem: number | { id: number } } | [addonItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

const addons = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    toggle: Object.assign(toggle, toggle),
}

export default addons