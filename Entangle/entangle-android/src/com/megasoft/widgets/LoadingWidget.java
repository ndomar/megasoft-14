package com.megasoft.widgets;

import android.app.Activity;
import android.app.ProgressDialog;

public class LoadingWidget {
	private static ProgressDialog window = null;
	private final static int WINDOW_WIDTH = 600;
	private final static int WINDOW_HEIGHT = 300;
	
	public static void show(Activity activity){
		if(window != null){
			hide();
		}  
		
		window = new ProgressDialog(activity);
		window.setMessage("Loading...");
		window.setCancelable(false);
		window.show();
		window.getWindow().setLayout(WINDOW_WIDTH, WINDOW_HEIGHT);
	}
	
	public static void hide(){
		if(window != null){
			window.dismiss();
		}
	}
}
