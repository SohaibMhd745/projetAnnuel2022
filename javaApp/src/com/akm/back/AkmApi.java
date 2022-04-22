package com.akm.back;

import org.json.*;

import java.io.StringWriter;
import java.util.HashMap;

public class AkmApi {
    private final String host = "localhost/";

    private enum Methods {
        POST, GET;
    }

    private enum Controllers {
        LOGIN("login")
        ;

        public final String url;

        Controllers(String url) {
            this.url = url;
        }
    }

    public enum Actions {
        SIGNUP(Methods.POST, "signup", Controllers.LOGIN),
        REGISTER_COMPANY(Methods.POST, "registercompany", Controllers.LOGIN),
        GENERATE_CODE(Methods.POST, "generatecode", Controllers.LOGIN),
        SIGN_IN(Methods.POST, "signin", Controllers.LOGIN),
        GET_USER_DATA(Methods.POST, "getdata", Controllers.LOGIN)
        ;

        public final Methods method;
        public final String url;
        public final Controllers controller;

        Actions(Methods method, String url, Controllers controller) {
            this.method = method;
            this.url = url;
            this.controller = controller;
        }
    }
}
