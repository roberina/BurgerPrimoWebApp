import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::index
* @see app/Http/Controllers/Admin/BurgerReviewController.php:13
* @route '/admin/burger-review'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/burger-review',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::index
* @see app/Http/Controllers/Admin/BurgerReviewController.php:13
* @route '/admin/burger-review'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::index
* @see app/Http/Controllers/Admin/BurgerReviewController.php:13
* @route '/admin/burger-review'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::index
* @see app/Http/Controllers/Admin/BurgerReviewController.php:13
* @route '/admin/burger-review'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::approve
* @see app/Http/Controllers/Admin/BurgerReviewController.php:44
* @route '/admin/burger-review/{burger}/approve'
*/
export const approve = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/admin/burger-review/{burger}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::approve
* @see app/Http/Controllers/Admin/BurgerReviewController.php:44
* @route '/admin/burger-review/{burger}/approve'
*/
approve.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return approve.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::approve
* @see app/Http/Controllers/Admin/BurgerReviewController.php:44
* @route '/admin/burger-review/{burger}/approve'
*/
approve.post = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::reject
* @see app/Http/Controllers/Admin/BurgerReviewController.php:50
* @route '/admin/burger-review/{burger}/reject'
*/
export const reject = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/admin/burger-review/{burger}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::reject
* @see app/Http/Controllers/Admin/BurgerReviewController.php:50
* @route '/admin/burger-review/{burger}/reject'
*/
reject.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return reject.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BurgerReviewController::reject
* @see app/Http/Controllers/Admin/BurgerReviewController.php:50
* @route '/admin/burger-review/{burger}/reject'
*/
reject.post = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

const burgerReview = {
    index: Object.assign(index, index),
    approve: Object.assign(approve, approve),
    reject: Object.assign(reject, reject),
}

export default burgerReview