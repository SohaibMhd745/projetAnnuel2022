package com.akm.ui;

import com.akm.back.*;

public class MainProcess {
    public static void main(String[] args) {
        ConnectionPage coPage = new ConnectionPage();
        coPage.show();
    }

    public static void callUserPage(String token){
        System.out.println(token);
    }
}
