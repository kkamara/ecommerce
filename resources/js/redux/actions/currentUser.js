import { APP_URL } from "../../constants";
import { currentUserActions } from "../reducers/types";
import { getAuthToken } from "../../utilities/methods";

export default { getCurrentUser };

function getCurrentUser() {
    return async dispatch => {
        dispatch(request(currentUserActions.GET_CURRENT_USER_PENDING));

        let url = APP_URL + "/user/authenticate";
        const token = getAuthToken();

        if (null === token) {
            dispatch(
                error(
                    currentUserActions.GET_CURRENT_USER_ERROR,
                    "No token stored"
                )
            );
        } else {
            url = encodeURI(url);
            console.log("querying server for " + url);
            await fetch(url, {
                method: "GET",
                headers: new Headers({
                    Authorization: `Bearer ${token}`
                })
            })
                .then(res => res.json())
                .then(json => {
                    if (undefined === json.user) {
                        dispatch(
                            error(
                                currentUserActions.GET_CURRENT_USER_ERROR,
                                json.error
                            )
                        );
                    } else {
                        dispatch(
                            success(
                                currentUserActions.GET_CURRENT_USER_SUCCESS,
                                json.user
                            )
                        );
                    }
                })
                .catch(err => {
                    dispatch(
                        error(currentUserActions.GET_CURRENT_USER_ERROR, err)
                    );
                });
        }

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
