package com.ashway.akmbarcodescanner;

import java.util.Map;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.Field;
import retrofit2.http.FieldMap;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface BarcodeService {
    @POST("login/getdata")
    Call<User> createBarcode(@Body Barcode barcode);

    @FormUrlEncoded
    @POST("login/getdata")
    Call<User> createBarcode(@Field("barcode") String barcode);

    @FormUrlEncoded
    @POST("login/getdata")
    Call<User> createBarcode(@FieldMap Map<String, String> fields);
}