package com.megasoft.utils;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.widget.Toast;

public class UI {

	/**
	 * toast to notify the user if the message was sent successfully or not
	 * 
	 * @param context
	 * @param text
	 * @param length
	 *            either Toast.LENGTH_SHORT or Toast.LENGTH_LONG
	 */

	public static void makeToast(Context context, String text, int length) {
		Toast toast = Toast.makeText(context, text, length);
		toast.show();
	}

	public static void buildDialog(Context context, String title, String message) {
		AlertDialog.Builder builder = new AlertDialog.Builder(context);
		builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int id) {
				dialog.cancel();
			}
		});
		builder.setMessage(message);
		builder.setTitle(title);
		AlertDialog dialog = builder.create();
		dialog.show(); // there is a bug here 
	}
}
