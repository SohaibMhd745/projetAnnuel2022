package com.ashway.akmbarcodescanner.httphandler;

public class User {
    private String id;
    private String firstname;
    private String lastname;
    private String inscription;
    private String birthdate;
    private String phone;
    private String barcode;

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

    public void setId(String id) {
        this.id = id;
    }

    public String getFirstname() {
        return firstname;
    }

    public void setFirstname(String firstname) {
        this.firstname = firstname;
    }

    public String getLastname() {
        return lastname;
    }

    public void setLastname(String lastname) {
        this.lastname = lastname;
    }

    public String getInscription() {
        return inscription;
    }

    public void setInscription(String inscription) {
        this.inscription = inscription;
    }

    public String getBirthdate() {
        return birthdate;
    }

    public void setBirthdate(String birthdate) {
        this.birthdate = birthdate;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getBarcode() {
        return barcode;
    }

    public void setBarcode(String barcode) {
        this.barcode = barcode;
    }
}