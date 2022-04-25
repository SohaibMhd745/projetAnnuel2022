package com.ashway.akmbarcodescanner;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import com.ashway.akmbarcodescanner.barcode.Barcode;
import com.ashway.akmbarcodescanner.barcode.BarcodeRepository;
import com.ashway.akmbarcodescanner.httphandler.ResponseHandler;
import com.ashway.akmbarcodescanner.httphandler.User;
import com.budiyev.android.codescanner.CodeScanner;
import com.budiyev.android.codescanner.CodeScannerView;
import com.budiyev.android.codescanner.DecodeCallback;
import com.google.gson.Gson;
import com.google.zxing.Result;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends AppCompatActivity {
    private CodeScanner mCodeScanner;
    private static final int MY_CAMERA_REQUEST_CODE = 100;
    private BarcodeRepository barcodeRepository;
    public static User user;

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == MY_CAMERA_REQUEST_CODE) {
            if (grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Toast.makeText(this, "Permission accordée : caméra", Toast.LENGTH_LONG).show();
            } else {
                Toast.makeText(this, "Permission refusée : caméra", Toast.LENGTH_LONG).show();
            }
        }
    }

    @RequiresApi(api = Build.VERSION_CODES.M)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        CodeScannerView scannerView = findViewById(R.id.scanner_view);
        mCodeScanner = new CodeScanner(this, scannerView);

        if (checkSelfPermission(Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED)
            requestPermissions(new String[]{Manifest.permission.CAMERA}, MY_CAMERA_REQUEST_CODE);

        mCodeScanner.setDecodeCallback(new DecodeCallback() {
            @Override
            public void onDecoded(@NonNull final Result result) {
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Barcode barcode = new Barcode(result.getText().toString().substring(6,12));
                        barcodeRepository = new BarcodeRepository();
                        barcodeRepository.getBarcodeService().createBarcode(barcode).enqueue(new Callback<ResponseHandler>() {
                            @Override
                            public void onResponse(@NonNull Call<ResponseHandler> call, @NonNull Response<ResponseHandler> r) {
                                if(r.body()!=null&&r.body().isSuccess()){
                                    Gson gson = new Gson();
                                    user = gson.fromJson(r.body().getUser().toString(), User.class);
                                    Intent i = new Intent(MainActivity.this, DisplayActivity.class);
                                    startActivity(i);
                                }else{
                                    Toast.makeText(getApplicationContext(), "Message Erreur lecture Code Barre", Toast.LENGTH_SHORT).show();
                                }
                            }
                            @Override
                            public void onFailure(@NonNull Call<ResponseHandler> call, @NonNull Throwable t) {
                                Toast.makeText(getApplicationContext(), "Error Sending Barcode: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                            }
                        });
                    }
                });
            }
        });
        scannerView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                mCodeScanner.startPreview();
            }
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
        mCodeScanner.startPreview();
    }

    @Override
    protected void onPause() {
        mCodeScanner.releaseResources();
        super.onPause();
    }
}