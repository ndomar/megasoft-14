package com.megasoft.widgets;

import android.app.Activity;
import android.app.AlertDialog;
import android.view.LayoutInflater;
import android.view.View;

import com.megasoft.entangle.R;

public class LoadingWidget {
	private static AlertDialog window = null;
	private final static int WINDOW_DIMENSION = 400;
	
	public static void show(Activity activity){
		if(window != null){
			hide();
		}  
		
		window = new AlertDialog.Builder(activity).create();
		
		LayoutInflater factory = LayoutInflater.from(activity);
		final View view = factory.inflate(R.layout.widget_loading, null);
		window.setView(view);
		
		window.show();
		window.getWindow().setLayout(WINDOW_DIMENSION, WINDOW_DIMENSION);
	}
	
	public static void hide(){
		if(window != null){
			window.dismiss();
		}
	}
}
