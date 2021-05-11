import { getCacheHashToken } from "../../utilities/methods";
import { APP_URL, TOKEN_NAME } from "../../constants";
import { userActions } from "../reducers/types";

export default { loginUser, logoutUser };

function loginUser(email, password) {
    return async dispatch => {
        dispatch(request(userActions.POST_LOGIN_USER_PENDING));
        let url = APP_URL + "/user/login";
        url += `?client_hash_key=${getCacheHashToken()}`;
        url = encodeURI(url);

        let body = new FormData();
        body.append("email", email);
        body.append("password", password);

        console.log("querying server for " + url);
        await fetch(url, {
            method: "POST",
            body
        })
            .then(res => res.json())
            .then(json => {
                localStorage.setItem(TOKEN_NAME, json.token);

                dispatch(
                    success(userActions.POST_LOGIN_USER_SUCCESS, json.user)
                );
            })
            .catch(err => {
                dispatch(error(userActions.POST_LOGIN_USER_ERROR, err));
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

function logoutUser() {
    return async dispatch => {
        localStorage.removeItem(TOKEN_NAME);

        dispatch(pending(userActions.GET_CURRENT_USER_PENDING));
        dispatch(success(userActions.GET_CURRENT_USER_SUCCESS));

        function pending(type) {
            return {
                type,
                payload
            };
        }

        function success(type) {
            return {
                type,
                payload
            };
        }
    };
}
