package com.akm.ui;

import com.akm.back.*;

import javax.swing.*;
import javax.swing.border.Border;
import java.awt.*;

public class GUI {
    public JFrame frame;
    public JPanel panel;
    public Border border;
    JPanel[][] tempPanels;

    public GUI(int cols, int rows){
        frame = new JFrame();
        panel = new JPanel();
        border = BorderFactory.createEmptyBorder(30,20,10,20 );
        panel.setBorder(border);
        panel.setLayout(new GridLayout(rows, cols));
        tempPanels = new JPanel[cols][rows];
    }

    public void show(){
        frame.add(panel, BorderLayout.CENTER);
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setTitle("AKM user management");
        frame.pack();
        frame.setVisible(true);
    }

    public void hide(){
        frame.setVisible(false);
    }

    public void addElem(int col, int row, JLabel elem){
        tempPanels[col][row] = new JPanel();
        tempPanels[col][row].add(elem);
        panel.add(tempPanels[col][row]);
    }
    public void addElem(int col, int row, JButton elem){
        tempPanels[col][row] = new JPanel();
        tempPanels[col][row].add(elem);
        panel.add(tempPanels[col][row]);
    }
    public void addElem(int col, int row, JTextField elem){
        tempPanels[col][row] = new JPanel();
        tempPanels[col][row].add(elem);
        panel.add(tempPanels[col][row]);
    }
    public void addElem(int col, int row, JPasswordField elem){
        tempPanels[col][row] = new JPanel();
        tempPanels[col][row].add(elem);
        panel.add(tempPanels[col][row]);
    }
}