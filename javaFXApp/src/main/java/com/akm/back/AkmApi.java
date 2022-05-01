package com.akm.back;

import org.json.JSONObject;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class AkmApi {

    /**
     * Sends request to akm api
     * @param action Action to be performed
     * @param jsonBody Stringified Json body
     * @return JSON Object
     */
    public static JSONObject requestApi(Actions action, String jsonBody){
        String host = "http://akmpa2022.westeurope.cloudapp.azure.com/";

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
        LOGIN("login"),
        CATALOG("catalog");

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
        GET_USER_DATA(Methods.POST, "getdata", Controllers.LOGIN),
        GET_CATALOG(Methods.POST, "orderedsearch", Controllers.CATALOG);

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