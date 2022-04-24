package com.ashway.akmbarcodescanner.barcode;

import com.ashway.akmbarcodescanner.httphandler.ResponseHandler;
import com.ashway.akmbarcodescanner.httphandler.User;

import java.util.Map;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.Field;
import retrofit2.http.FieldMap;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface BarcodeService {
    @POST("login/getdata")
    Call<ResponseHandler> createBarcode(@Body Barcode barcode);

    @FormUrlEncoded
    @POST("login/getdata")
    Call<ResponseHandler> createBarcode(@Field("barcode") String barcode);

    @FormUrlEncoded
    @POST("login/getdata")
    Call<ResponseHandler> createBarcode(@FieldMap Map<String, String> fields);
}