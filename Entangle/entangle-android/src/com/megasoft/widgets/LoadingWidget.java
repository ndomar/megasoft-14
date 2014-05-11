package com.megasoft.widgets;

import android.app.Activity;
import android.app.ProgressDialog;

/*
 * Class for a loading widget to appear
 * @author OmarElAzazy
 */
public class LoadingWidget {
	private static ProgressDialog window = null;
	private final static int WINDOW_WIDTH = 600;
	private final static int WINDOW_HEIGHT = 300;
	
	/*
	 * Static method that shows a loading widget with a custom message
	 * @param Activity activity the running activity
	 * @param String message the custom message
	 * @return
	 * @author OmarElAzazy
	 */
	public static void show(Activity activity, String message){
		if(window != null){
			hide();
		}  
		
		window = new ProgressDialog(activity);
		window.setMessage(message);
		window.setCancelable(false);
		window.show();
		window.getWindow().setLayout(WINDOW_WIDTH, WINDOW_HEIGHT);
	}
	
	/*
	 * A static method that removes the loading widget if exists
	 * @return
	 * @author OmarElAzazy
	 */
	public static void hide(){
		if(window != null){
			window.dismiss();
		}
	}
}
