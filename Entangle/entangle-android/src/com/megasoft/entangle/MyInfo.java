package com.example.editinfo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.TextView;

public class MyInfo extends Activity {
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.myinfo);
		Bundle extras = getIntent().getExtras();
		String desc;
		LinearLayout layout = (LinearLayout) findViewById(R.id.myinfo);
		TextView v;
		if (extras != null) {
			if (extras.containsKey("Desc")) {
				desc = extras.get("Desc").toString();
				v = (TextView) findViewById(R.id.CurrentDescription);
				v.setText(desc);
			}
			if (extras.containsKey("Age")) {
				desc = extras.get("Age").toString();
				v = (TextView) findViewById(R.id.AgeViewer);
				v.setText(desc);
			}
			if (extras.containsKey("newMail")) {
				desc = extras.get("newMail").toString();
				TextView view = new TextView(this);
				view.setText(desc);
				layout.addView(view);

			}
		}
	}

	public void EditMyDescription(View view) {
		Intent editDes = new Intent(MyInfo.this, EditDescription.class);
		startActivityForResult(editDes, 0);
	}

	public void EdityourAge(View view) {
		Intent editAge = new Intent(this, EditAge.class);
		startActivity(editAge);

	}

	public void EditMyPassword(View view) {
		Intent editpass = new Intent(this, EditMyPassword.class);
		startActivity(editpass);
	}

	public void AddEmail(View view) {
		Intent editpass = new Intent(this, AddEmail.class);
		startActivity(editpass);
	}
}
