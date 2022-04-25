package com.ashway.akmbarcodescanner.barcode;

import com.ashway.akmbarcodescanner.httphandler.ResponseHandler;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface BarcodeService {
    @POST("login/getdata")
    Call<ResponseHandler> createBarcode(@Body Barcode barcode);
}