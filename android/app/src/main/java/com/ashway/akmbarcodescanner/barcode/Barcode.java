package com.ashway.akmbarcodescanner.barcode;

import com.google.gson.annotations.SerializedName;

public class Barcode {
    @SerializedName("barcode")
    private final String barcode;

    public Barcode(String barcode) {
        this.barcode = barcode;
    }
}