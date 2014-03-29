package com.example.first;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.Toast;
import android.widget.ToggleButton;

public class MainActivity extends Activity {
	boolean toggled = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
	}

	public void notificationsToggle(View v) {
		GetRequest request = new GetRequest(
				"http://ay7aga.apiary.io/notification/10") {
			protected void onPostExecute(String response) {
				if(this.getStatusCode() == 200){
				if (toggled)
					Toast.makeText(getApplicationContext(),
							"Email Notifications Turned On " + response,
							Toast.LENGTH_SHORT).show();
				else
					Toast.makeText(getApplicationContext(),
							"Email Notifications Turned Off " + response,
							Toast.LENGTH_SHORT).show();
			}
				else if(this.getStatusCode() == 404)
					Toast.makeText(getApplicationContext(),
							"404 Error : Not Found",
							Toast.LENGTH_SHORT).show();
					
			}
		};
		request.addHeader("X-SESSION-ID", "10");
		request.execute();
		toggled = !toggled;
	}
}
