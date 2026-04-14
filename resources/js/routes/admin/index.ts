import dashboard from './dashboard'
import orders from './orders'
import burgerReview from './burger-review'
import menu from './menu'
import ingredients from './ingredients'
import reviews from './reviews'
import announcements from './announcements'
import addons from './addons'

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    orders: Object.assign(orders, orders),
    burgerReview: Object.assign(burgerReview, burgerReview),
    menu: Object.assign(menu, menu),
    ingredients: Object.assign(ingredients, ingredients),
    reviews: Object.assign(reviews, reviews),
    announcements: Object.assign(announcements, announcements),
    addons: Object.assign(addons, addons),
}

export default admin