import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReviewController::store
* @see app/Http/Controllers/ReviewController.php:10
* @route '/reviews'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReviewController::store
* @see app/Http/Controllers/ReviewController.php:10
* @route '/reviews'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReviewController::store
* @see app/Http/Controllers/ReviewController.php:10
* @route '/reviews'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const ReviewController = { store }

export default ReviewController