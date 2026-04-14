import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:14
* @route '/cart'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:14
* @route '/cart'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:14
* @route '/cart'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:14
* @route '/cart'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CartController::add
* @see app/Http/Controllers/CartController.php:74
* @route '/cart/add'
*/
export const add = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: add.url(options),
    method: 'post',
})

add.definition = {
    methods: ["post"],
    url: '/cart/add',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::add
* @see app/Http/Controllers/CartController.php:74
* @route '/cart/add'
*/
add.url = (options?: RouteQueryOptions) => {
    return add.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::add
* @see app/Http/Controllers/CartController.php:74
* @route '/cart/add'
*/
add.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: add.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CartController::addMenuItem
* @see app/Http/Controllers/CartController.php:114
* @route '/cart/add-menu-item'
*/
export const addMenuItem = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addMenuItem.url(options),
    method: 'post',
})

addMenuItem.definition = {
    methods: ["post"],
    url: '/cart/add-menu-item',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::addMenuItem
* @see app/Http/Controllers/CartController.php:114
* @route '/cart/add-menu-item'
*/
addMenuItem.url = (options?: RouteQueryOptions) => {
    return addMenuItem.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::addMenuItem
* @see app/Http/Controllers/CartController.php:114
* @route '/cart/add-menu-item'
*/
addMenuItem.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addMenuItem.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CartController::addNew
* @see app/Http/Controllers/CartController.php:159
* @route '/cart/add-new'
*/
export const addNew = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addNew.url(options),
    method: 'post',
})

addNew.definition = {
    methods: ["post"],
    url: '/cart/add-new',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::addNew
* @see app/Http/Controllers/CartController.php:159
* @route '/cart/add-new'
*/
addNew.url = (options?: RouteQueryOptions) => {
    return addNew.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::addNew
* @see app/Http/Controllers/CartController.php:159
* @route '/cart/add-new'
*/
addNew.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addNew.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:202
* @route '/cart/{burger}/update'
*/
export const update = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/cart/{burger}/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:202
* @route '/cart/{burger}/update'
*/
update.url = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: args.burger,
    }

    return update.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:202
* @route '/cart/{burger}/update'
*/
update.post = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CartController::remove
* @see app/Http/Controllers/CartController.php:231
* @route '/cart/{burger}'
*/
export const remove = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

remove.definition = {
    methods: ["delete"],
    url: '/cart/{burger}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CartController::remove
* @see app/Http/Controllers/CartController.php:231
* @route '/cart/{burger}'
*/
remove.url = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: args.burger,
    }

    return remove.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::remove
* @see app/Http/Controllers/CartController.php:231
* @route '/cart/{burger}'
*/
remove.delete = (args: { burger: string | number } | [burger: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CartController::clear
* @see app/Http/Controllers/CartController.php:255
* @route '/cart/clear'
*/
export const clear = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clear.url(options),
    method: 'post',
})

clear.definition = {
    methods: ["post"],
    url: '/cart/clear',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::clear
* @see app/Http/Controllers/CartController.php:255
* @route '/cart/clear'
*/
clear.url = (options?: RouteQueryOptions) => {
    return clear.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::clear
* @see app/Http/Controllers/CartController.php:255
* @route '/cart/clear'
*/
clear.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clear.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CartController::checkout
* @see app/Http/Controllers/CartController.php:265
* @route '/cart/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

checkout.definition = {
    methods: ["post"],
    url: '/cart/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CartController::checkout
* @see app/Http/Controllers/CartController.php:265
* @route '/cart/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::checkout
* @see app/Http/Controllers/CartController.php:265
* @route '/cart/checkout'
*/
checkout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

const cart = {
    index: Object.assign(index, index),
    add: Object.assign(add, add),
    addMenuItem: Object.assign(addMenuItem, addMenuItem),
    addNew: Object.assign(addNew, addNew),
    update: Object.assign(update, update),
    remove: Object.assign(remove, remove),
    clear: Object.assign(clear, clear),
    checkout: Object.assign(checkout, checkout),
}

export default cart