import { productsActions } from "./types";

const initialState = {
    products: {
        products: {}
    }
};
const productsReducer = (state = initialState, action) => {
    switch (action.type) {
        case productsActions.GET_PRODUCTS_PENDING:
            return { ...state, fetched: false, isLoaded: false };
        case productsActions.GET_PRODUCTS_ERROR:
            return {
                ...state,
                fetched: false,
                isLoaded: true,
                error: action.payload
            };
        case productsActions.GET_PRODUCTS_SUCCESS:
            return {
                ...state,
                fetched: true,
                isLoaded: true,
                products: action.payload
            };
    }

    return state;
};
export default productsReducer;
