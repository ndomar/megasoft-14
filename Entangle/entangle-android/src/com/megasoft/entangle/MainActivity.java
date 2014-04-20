package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;

import com.google.android.gms.gcm.GoogleCloudMessaging;

public class MainActivity extends Activity {

	private final static int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
	public static final String PROPERTY_REG_ID = "registration_id";
	String SENDER_ID = "87338614452";
	static final String TAG = "GCM";
	GoogleCloudMessaging gcm;
	String regid;
	public static final String uri = "http://mohamed.local/entangle/app_dev.php/register";

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		startActivity(new Intent(getApplicationContext(),
				GCMRegistrationActivity.class));
	}

}
