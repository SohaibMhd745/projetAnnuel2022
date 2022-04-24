package com.akm.back;

import org.json.*;
import com.akm.back.AkmException;

import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLParameters;
import java.io.Closeable;
import java.io.IOException;
import java.io.StringWriter;
import java.net.Authenticator;
import java.net.CookieHandler;
import java.net.ProxySelector;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpHeaders;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;
import java.util.HashMap;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.Executor;

public class AkmApi {

    /**
     * Sends request to akm api
     * @param action action to be performed
     * @param jsonBody Stringified Json body
     * @return Json Object of
     */
    public static JSONObject requestApi(Actions action, String jsonBody){
        String host = "http://localhost";

        HttpClient client = HttpClient.newHttpClient();
        HttpRequest req = HttpRequest.newBuilder()
                .uri(URI.create(host+"/"+action.controller.url+"/"+action.url))
                .POST(HttpRequest.BodyPublishers.ofString(jsonBody))
                .setHeader("Content-Type","application/json")
                .build();

        try{
            HttpResponse<String> response = client.send(req,
                    HttpResponse.BodyHandlers.ofString());
            return new JSONObject(response.body());
        } catch (Exception e){
            System.out.println(e.getMessage());
            return new JSONObject("\"success\":false, \"errorCode\":"+AkmException.BAD_REQUEST.code);
        }
    }

    ///Enums

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
