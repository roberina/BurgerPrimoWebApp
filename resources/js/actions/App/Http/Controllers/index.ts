import Admin from './Admin'
import CourierController from './CourierController'
import MenuController from './MenuController'
import ContactController from './ContactController'
import ReviewController from './ReviewController'
import MenuFavoriteController from './MenuFavoriteController'
import BurgerBuilderController from './BurgerBuilderController'
import CartController from './CartController'
import PaymentController from './PaymentController'
import OrderController from './OrderController'
import Settings from './Settings'

const Controllers = {
    Admin: Object.assign(Admin, Admin),
    CourierController: Object.assign(CourierController, CourierController),
    MenuController: Object.assign(MenuController, MenuController),
    ContactController: Object.assign(ContactController, ContactController),
    ReviewController: Object.assign(ReviewController, ReviewController),
    MenuFavoriteController: Object.assign(MenuFavoriteController, MenuFavoriteController),
    BurgerBuilderController: Object.assign(BurgerBuilderController, BurgerBuilderController),
    CartController: Object.assign(CartController, CartController),
    PaymentController: Object.assign(PaymentController, PaymentController),
    OrderController: Object.assign(OrderController, OrderController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers