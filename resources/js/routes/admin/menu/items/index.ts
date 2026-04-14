import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MenuItemController::index
* @see app/Http/Controllers/Admin/MenuItemController.php:15
* @route '/admin/menu/items'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/menu/items',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::index
* @see app/Http/Controllers/Admin/MenuItemController.php:15
* @route '/admin/menu/items'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::index
* @see app/Http/Controllers/Admin/MenuItemController.php:15
* @route '/admin/menu/items'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::index
* @see app/Http/Controllers/Admin/MenuItemController.php:15
* @route '/admin/menu/items'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::create
* @see app/Http/Controllers/Admin/MenuItemController.php:74
* @route '/admin/menu/items/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/menu/items/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::create
* @see app/Http/Controllers/Admin/MenuItemController.php:74
* @route '/admin/menu/items/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::create
* @see app/Http/Controllers/Admin/MenuItemController.php:74
* @route '/admin/menu/items/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::create
* @see app/Http/Controllers/Admin/MenuItemController.php:74
* @route '/admin/menu/items/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::store
* @see app/Http/Controllers/Admin/MenuItemController.php:83
* @route '/admin/menu/items'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/menu/items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::store
* @see app/Http/Controllers/Admin/MenuItemController.php:83
* @route '/admin/menu/items'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::store
* @see app/Http/Controllers/Admin/MenuItemController.php:83
* @route '/admin/menu/items'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::edit
* @see app/Http/Controllers/Admin/MenuItemController.php:109
* @route '/admin/menu/items/{item}/edit'
*/
export const edit = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/menu/items/{item}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::edit
* @see app/Http/Controllers/Admin/MenuItemController.php:109
* @route '/admin/menu/items/{item}/edit'
*/
edit.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { item: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: typeof args.item === 'object'
        ? args.item.id
        : args.item,
    }

    return edit.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::edit
* @see app/Http/Controllers/Admin/MenuItemController.php:109
* @route '/admin/menu/items/{item}/edit'
*/
edit.get = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::edit
* @see app/Http/Controllers/Admin/MenuItemController.php:109
* @route '/admin/menu/items/{item}/edit'
*/
edit.head = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::update
* @see app/Http/Controllers/Admin/MenuItemController.php:119
* @route '/admin/menu/items/{item}'
*/
export const update = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/admin/menu/items/{item}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::update
* @see app/Http/Controllers/Admin/MenuItemController.php:119
* @route '/admin/menu/items/{item}'
*/
update.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { item: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: typeof args.item === 'object'
        ? args.item.id
        : args.item,
    }

    return update.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::update
* @see app/Http/Controllers/Admin/MenuItemController.php:119
* @route '/admin/menu/items/{item}'
*/
update.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::destroy
* @see app/Http/Controllers/Admin/MenuItemController.php:148
* @route '/admin/menu/items/{item}'
*/
export const destroy = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/menu/items/{item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::destroy
* @see app/Http/Controllers/Admin/MenuItemController.php:148
* @route '/admin/menu/items/{item}'
*/
destroy.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { item: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: typeof args.item === 'object'
        ? args.item.id
        : args.item,
    }

    return destroy.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::destroy
* @see app/Http/Controllers/Admin/MenuItemController.php:148
* @route '/admin/menu/items/{item}'
*/
destroy.delete = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\MenuItemController::updateOrder
* @see app/Http/Controllers/Admin/MenuItemController.php:160
* @route '/admin/menu/items/update-order'
*/
export const updateOrder = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrder.url(options),
    method: 'post',
})

updateOrder.definition = {
    methods: ["post"],
    url: '/admin/menu/items/update-order',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MenuItemController::updateOrder
* @see app/Http/Controllers/Admin/MenuItemController.php:160
* @route '/admin/menu/items/update-order'
*/
updateOrder.url = (options?: RouteQueryOptions) => {
    return updateOrder.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuItemController::updateOrder
* @see app/Http/Controllers/Admin/MenuItemController.php:160
* @route '/admin/menu/items/update-order'
*/
updateOrder.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrder.url(options),
    method: 'post',
})

const items = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    updateOrder: Object.assign(updateOrder, updateOrder),
}

export default items