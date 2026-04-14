import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::checkout
* @see app/Http/Controllers/PaymentController.php:16
* @route '/payment/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

checkout.definition = {
    methods: ["get","head"],
    url: '/payment/checkout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::checkout
* @see app/Http/Controllers/PaymentController.php:16
* @route '/payment/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::checkout
* @see app/Http/Controllers/PaymentController.php:16
* @route '/payment/checkout'
*/
checkout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::checkout
* @see app/Http/Controllers/PaymentController.php:16
* @route '/payment/checkout'
*/
checkout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::createIntent
* @see app/Http/Controllers/PaymentController.php:75
* @route '/payment/create-intent'
*/
export const createIntent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createIntent.url(options),
    method: 'post',
})

createIntent.definition = {
    methods: ["post"],
    url: '/payment/create-intent',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::createIntent
* @see app/Http/Controllers/PaymentController.php:75
* @route '/payment/create-intent'
*/
createIntent.url = (options?: RouteQueryOptions) => {
    return createIntent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::createIntent
* @see app/Http/Controllers/PaymentController.php:75
* @route '/payment/create-intent'
*/
createIntent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createIntent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::process
* @see app/Http/Controllers/PaymentController.php:136
* @route '/payment/process'
*/
export const process = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(options),
    method: 'post',
})

process.definition = {
    methods: ["post"],
    url: '/payment/process',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::process
* @see app/Http/Controllers/PaymentController.php:136
* @route '/payment/process'
*/
process.url = (options?: RouteQueryOptions) => {
    return process.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::process
* @see app/Http/Controllers/PaymentController.php:136
* @route '/payment/process'
*/
process.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(options),
    method: 'post',
})

const payment = {
    checkout: Object.assign(checkout, checkout),
    createIntent: Object.assign(createIntent, createIntent),
    process: Object.assign(process, process),
}

export default payment