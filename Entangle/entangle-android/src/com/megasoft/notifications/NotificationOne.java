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
		
		this.setTitle("New Entangle Notification "+requesterName);

		int id = 0;

		Bundle extras = getIntent().getExtras();

		if (extras == null) {

			String s = "Activity Error";

		}

		else {

			id = extras.getInt("notificationId");

		}

		TextView t = (TextView) findViewById(R.id.text1);

		s = "Requester \"" + requesterName
				+ "\" Has Accepted Another Offere's Offer In Ihe Tangle \""
				+ tangleName + "\" On The Request With ID : \"" + requestId
				+ "\" , description \"" + (String) requestDesc + "\" , and status \""
				+ requestStatus+"\"";

		t.setText(s);
		t.setTextSize(20);
		NotificationManager myNotificationManager =

		(NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		// remove the notification with the specific id

		myNotificationManager.cancel(id);

	}
}
