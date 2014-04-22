package com.megasoft.entangle;


import com.megasoft.requests.DeleteRequest;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.Dialog;
import android.content.DialogInterface;
import android.view.View;
import android.widget.Toast;

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
	@SuppressWarnings("deprecation")
	public void leaveTangle(View view) {
		showDialog(0);
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
		// to be changed to the tangleId
		DeleteRequest leaveRequest = new DeleteRequest(
				"http://entangle2.apiary-mock.com/tangle/"
						+ getIntent().getIntExtra("tangleId", 0) + "user") {
			public void onPostExecute(String response) {
				if (response != null) {
					if (getStatusCode() == 201) {
						Toast.makeText(getBaseContext(),
								"You left the tangle successfully",
								Toast.LENGTH_LONG).show();
						// redirect to the tangles stream Activity
					} else if (getStatusCode() == 403) {
						Toast.makeText(
								getBaseContext(),
								"Sorry, you are not allowed to leave the tangle",
								Toast.LENGTH_LONG).show();
					} else {
						Toast.makeText(
								getBaseContext(),
								"Sorry, problem happened while leaving the tangle. Try again later",
								Toast.LENGTH_LONG).show();
					}
				}
			}
		};
		// to be changed to sessionId
		leaveRequest.addHeader("X-SESSION-ID", "sessionId");
		leaveRequest.execute();
	}
}