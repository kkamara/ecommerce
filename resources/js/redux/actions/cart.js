import { getCacheHashToken, getAuthToken } from "../../utilities/methods";
import { cartActions } from "../reducers/types";
import { APP_URL } from "../../constants";

export default {
    addToCart,
    getCart
};

function addToCart(ID) {
    return async dispatch => {
        dispatch(request(cartActions.ADD_TO_CART_PENDING));

        let url = APP_URL + `/products/${ID}/store`;
        url += `?client_hash_key=${getCacheHashToken()}`;
        url = encodeURI(url)

        console.log("querying server for " + url);
        await fetch(url, {
            method: "POST"
        })
            .then(res => res.json())
            .then(json => {
                dispatch(
                    success(cartActions.ADD_TO_CART_SUCCESS, json.message)
                );
            })
            .catch(err => {
                dispatch(error(cartActions.ADD_TO_CART_ERROR, err));
            });

        function request(type) {
            return {
                type
            };
        }

        function error(type, payload) {
            return {
                type,
                payload
            };
        }

        function success(type, payload) {
            dispatch(getCart());
            return {
                type,
                payload
            };
        }
    };
}

function getCart(user) {
    return async dispatch => {
        dispatch(request(cartActions.GET_CART_PENDING));
        let url = APP_URL + `/cart`;
        console.log("user", user);
        let headers;
        if (user) {
            headers = new Headers({
                Authorization: `Bearer ${getAuthToken()}`
            });
        } else {
            url += `?client_hash_key=${getCacheHashToken()}`;
        }

        url = encodeURI(url);

        console.log("querying server for " + url);
        await fetch(url, {
            method: "POST",
            headers
        })
            .then(res => res.json())
            .then(json => {
                dispatch(
                    success(cartActions.GET_CART_SUCCESS, {
                        count: json.count,
                        items: json.cart,
                        cost: json.cost
                    })
                );
            })
            .catch(err => {
                dispatch(error(cartActions.GET_CART_ERROR, err));
            });

        function request(type) {
            return {
                type
            };
        }

        function error(type, payload) {
            return {
                type,
                payload
            };
        }

        function success(type, payload) {
            return {
                type,
                payload
            };
        }
    };
}
