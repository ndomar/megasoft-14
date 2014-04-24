package com.megasoft.notifications;

import android.app.Activity;
import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;

import com.megasoft.entangle.R;

public class NotificationOne extends Activity {
	String tangleName;
	String requesterName;
	String requestDesc;
	String requestStatus;
	String requestId;
	String s;

	@Override
	public void onCreate(Bundle savedInstanceState)

	{
		super.onCreate(savedInstanceState);

		setContentView(R.layout.notification_one);
		
		//Retrieving the information from the previous activity
		
		if (getIntent().getStringExtra("tangleName") != null)
			tangleName = getIntent().getStringExtra("tangleName");

		if (getIntent().getStringExtra("requesterName") != null)
			requesterName = getIntent().getStringExtra("requesterName");

		if (getIntent().getStringExtra("requestStatus") != null)
			requestStatus = getIntent().getStringExtra("requestStatus");

		if (getIntent().getStringExtra("requestId") != null)
			requestId = getIntent().getStringExtra("requestId");

		if (getIntent().getStringExtra("requestDesc") != null)
			requestDesc = getIntent().getStringExtra("requestDesc");

		int id = 0;
		
		//Checking if the extras that will be viewed are there , then using the notificationId
		Bundle extras = getIntent().getExtras();

		if (extras == null) {

			String s = "Activity Error";

		}

		else {

			id = extras.getInt("notificationId");

		}
		
		//Adding the information into the activity then viewing them
		TextView t = (TextView) findViewById(R.id.text1);

		s = "Requester \"" + requesterName
				+ "\" Has Accepted Another Offerer's Offer In Ihe Tangle \""
				+ tangleName + "\" , On The Request With ID : \"" + requestId
				+ "\" , Description \"" + (String) requestDesc
				+ "\" , And Status \"" + requestStatus + "\" .";

		t.setText(s);
		t.setTextSize(20);
		NotificationManager myNotificationManager =

		(NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		// remove the notification with the specific id

		myNotificationManager.cancel(id);

	}
}
