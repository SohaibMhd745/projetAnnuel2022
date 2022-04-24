package com.ashway.akmbarcodescanner.barcode;

import com.google.gson.annotations.SerializedName;

public class Barcode {
    @SerializedName("barcode")
    private String barcode;

    public String getBarcode() {
        return barcode;
    }

    public void setBarcode(String barcode) {
        this.barcode = barcode;
    }

    public Barcode(String barcode) {
        this.barcode = barcode;
    }
}