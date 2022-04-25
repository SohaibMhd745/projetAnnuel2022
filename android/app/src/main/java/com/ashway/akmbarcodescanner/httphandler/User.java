package com.ashway.akmbarcodescanner.httphandler;

public class User {
    private final String id;
    private final String firstname;
    private final String lastname;
    private final String inscription;
    private final String birthdate;
    private final String phone;
    private final String barcode;

    public User(String id, String firstname, String lastname, String inscription, String birthdate, String phone, String barcode) {
        this.id = id;
        this.firstname = firstname;
        this.lastname = lastname;
        this.inscription = inscription;
        this.birthdate = birthdate;
        this.phone = phone;
        this.barcode = barcode;
    }

    public String getId() {
        return id;
    }

    public String getFirstname() {
        return firstname;
    }

    public String getLastname() {
        return lastname;
    }

    public String getInscription() {
        return inscription;
    }

    public String getBirthdate() {
        return birthdate;
    }

    public String getPhone() {
        return phone;
    }

    public String getBarcode() {
        return barcode;
    }
}