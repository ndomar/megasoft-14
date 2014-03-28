package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

public class ContactActivity extends Activity implements OnClickListener {
	public static String claimURL = "http://entangle2.apiary-mock.com/claim/4/contact";
	public static String contactURL = "http://entangle2.apiary-mock.com/claim/4/contact/send";
	int reqID = -1;
	int offID = -1;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_contact);
		TextView tvReqName = (TextView) findViewById(R.id.TVRequesterName);
		TextView tvReqDesc = (TextView) findViewById(R.id.TVRequestDesc);
		TextView tvoffName = (TextView) findViewById(R.id.TVOffererName);
		TextView tvClaimMessage = (TextView) findViewById(R.id.TVClaimMessage);
		TextView tvOffDesc = (TextView) findViewById(R.id.TVOfferDesc);
		Button bContReq = (Button) findViewById(R.id.BContactRequester);
		Button bContOffer = (Button) findViewById(R.id.BContactOfferer);
		bContOffer.setOnClickListener(this);
		bContReq.setOnClickListener(this);
		JSONObject obj = new JSONObject();
		GetRequest req = new GetRequest(claimURL);
		req.addHeader("X-SESSION-ID", "helloWorld");
		String x = "";
		try {
			x = req.execute().get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}
		try {
			obj = new JSONObject(x);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		try {
			tvReqName.setText(obj.getString("requesterName"));
			tvReqDesc.setText(obj.getString("requestDesc"));
			tvoffName.setText(obj.getString("offererName"));
			tvOffDesc.setText(obj.getString("offerDesc"));
			tvClaimMessage.setText(obj.getString("claimMessage"));
			reqID = obj.getInt("requesterID");
			offID = obj.getInt("offererID");
		} catch (JSONException e) {
			e.printStackTrace();
		}

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.contact, menu);
		return true;
	}

	@Override
	public void onClick(View v) {
		EditText etMessage = (EditText) findViewById(R.id.ETMessage);
		String message = etMessage.getText().toString();
		int userID = -1;
		switch (v.getId()) {
		case R.id.BContactOfferer:
			userID = reqID;
			break;
		case R.id.BContactRequester:
			userID = offID;
			break;
		}
		sendUserMessage(userID, message);
	}

	public void sendUserMessage(int userID, String message) {
		JSONObject obj = new JSONObject();
		try {
			obj.put("userID", userID);
			obj.put("message", message);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest req = new PostRequest(contactURL, obj);
		req.addHeader("X-SESSION-ID", "helloWorld");
		JSONObject response = new JSONObject();
		String text = "";
		try {
			response = new JSONObject(req.execute().get());
			text = response.getString("message");
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		}
		makeToast(text, Toast.LENGTH_LONG);
		Log.d("info", "" + Toast.LENGTH_SHORT);
	}

	public void makeToast(String text, int length) {
		Context context = getApplicationContext();
		Toast toast = Toast.makeText(context, text, length);
		toast.show();
	}
}
