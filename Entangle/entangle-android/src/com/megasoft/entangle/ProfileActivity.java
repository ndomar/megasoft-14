package com.megasoft.entangle;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.Dialog;
import android.content.DialogInterface;
import android.view.View;

public class ProfileActivity extends Activity {

	/**
	 * This is method is invoked by the button of leave tangle when it is
	 * clicked
	 * 
	 * @param view
	 *            , in this case is the leave tangle button
	 * 
	 * @author HebaAamer
	 */
	public void leaveTangle(View view) {

	}

	@Override
	protected Dialog onCreateDialog(int dialogId) {
		Builder dialogBuilder = new AlertDialog.Builder(this);
		dialogBuilder.setTitle("Leaving the tangle");
		dialogBuilder
				.setMessage("Are you sure you want to leave this tangle ?");
		dialogBuilder.setPositiveButton("Yes",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						sendLeaveRequest();
					}
				});
		dialogBuilder.setNegativeButton("NO",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
					}
				});
		return dialogBuilder.create();
	}

	private void sendLeaveRequest() {

	}
}
