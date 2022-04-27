package com.akm.front;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import java.io.IOException;
import java.util.Objects;

public class AKMApplication extends Application {

    @Override
    public void start(Stage stage) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(AKMApplication.class.getResource("start.fxml"));
        Scene scene = new Scene(fxmlLoader.load(), 1280, 720);
        Image icon = new Image(Objects.requireNonNull(getClass().getResourceAsStream("/assets/logo.png")));
        stage.getIcons().add(icon);
        stage.setTitle("AKM Gestion - Bienvenue");
        stage.setScene(scene);
        stage.show();
    }

    public static void main(String[] args) {
        launch();
    }
}