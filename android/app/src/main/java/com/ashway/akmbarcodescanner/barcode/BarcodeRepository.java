package com.ashway.akmbarcodescanner.barcode;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public final class BarcodeRepository {
    private static BarcodeRepository instance;

    private final BarcodeService barcodeService;

    public static BarcodeRepository getInstance() {
        if (instance == null) {
            instance = new BarcodeRepository();
        }
        return instance;
    }

    public BarcodeRepository() {
        HttpLoggingInterceptor interceptor = new HttpLoggingInterceptor();
        interceptor.setLevel(HttpLoggingInterceptor.Level.BODY);
        OkHttpClient client = new OkHttpClient.Builder().addInterceptor(interceptor).build();

        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("http://akmpa2022.westeurope.cloudapp.azure.com")
                .client(client)
                .addConverterFactory(GsonConverterFactory.create())
                .build();

        barcodeService = retrofit.create(BarcodeService.class);
    }

    public BarcodeService getBarcodeService() {
        return barcodeService;
    }
}