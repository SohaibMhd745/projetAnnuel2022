package com.ashway.akmbarcodescanner;

import androidx.appcompat.app.AppCompatActivity;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;

import com.ashway.akmbarcodescanner.httphandler.User;

public class DisplayActivity extends AppCompatActivity {

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_display);

        TextView lastname = findViewById(R.id.lastname);
        lastname.setText("Nom : " + MainActivity.user.getLastname());

        TextView firstname = findViewById(R.id.firstname);
        firstname.setText("Prénom : " + MainActivity.user.getFirstname());

        TextView birthdate = findViewById(R.id.birthdate);
        birthdate.setText("Date de naissance : " + MainActivity.user.getBirthdate());

        TextView number = findViewById(R.id.number);
        number.setText("Numéro de téléphone : " + MainActivity.user.getPhone());
    }
}