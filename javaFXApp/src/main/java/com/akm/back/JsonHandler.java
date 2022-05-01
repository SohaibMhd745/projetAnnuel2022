package com.akm.back;

import org.json.JSONObject;

import java.io.StringWriter;
import java.util.HashMap;

public class JsonHandler {

    ///Public Methods

    /**
     * Transforms provided key+value combinations of hashmaps into Json
     * @param strParams HashMap<String, String> Map of string params
     * @param intParams HashMap<String, Integer> Map of integer params
     * @return Json format string
     */
    public static String toJsonString(HashMap<String, String> strParams, HashMap<String, Integer> intParams){
        JSONObject object = buildStringParams(strParams);
        for(HashMap.Entry<String, Integer> param : intParams.entrySet()) {
            object.put(param.getKey(), param.getValue());
        }
        return writeJsonToString(object);
    }

    /**
     * Transforms provided key+value combinations of hashmaps into Json
     * @param strParams HashMap<String, String> Map of string params
     * @return Json format string
     */
    public static String toJsonString(HashMap<String, String> strParams){
        JSONObject object = buildStringParams(strParams);
        return writeJsonToString(object);
    }

    ///Private Methods

    /**
     * Stringifies Json object
     * @param object Json object to stringify
     * @return Stringified Json object
     */
    public static String writeJsonToString(JSONObject object){
        StringWriter output = new StringWriter();
        object.write(output);
        return output.toString();
    }

    /**
     * Builds string params hashMap into Json object
     * @param strParams Hashmap to be built into json object
     * @return sub json object with string params built into
     */
    private static JSONObject buildStringParams(HashMap<String, String> strParams) {
        JSONObject subObject = new JSONObject();
        for(HashMap.Entry<String, String> param : strParams.entrySet()) {
            subObject.put(param.getKey(), param.getValue());
        }
        return subObject;
    }
}