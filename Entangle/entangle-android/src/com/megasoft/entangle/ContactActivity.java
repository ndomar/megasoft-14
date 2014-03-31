package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.requests.GetRequest;
import com.megasoft.utils.UI;
import com.megasoft.utils.Util;

public class ContactActivity extends Activity implements OnClickListener {
	public static String claimURL = "http://mohamed.local/entangle/app_dev.php/claim/3/contact";
	public static String contactURL = "http://mohamed.local/entangle/app_dev.php/claim/3/contact/send";
	private int reqID = -1;
	private int offID = -1;
	protected TextView tvReqName;
	protected TextView tvReqDesc;
	protected TextView tvoffName;
	protected TextView tvClaimMessage;
	protected TextView tvOffDesc;
	protected Button bContReq;
	protected Button bContOffer;

	/**
	 * this activity will redirect to mainActivity if there is no user logged in
	 */

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		Intent login = new Intent(getApplicationContext(), MainActivity.class);
		if (!Util.isUserLoggedIn(this))
			startActivity(login);
		init();
		retrieveServerData();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.contact, menu);
		return true;
	}

	/**
	 * init the view of this activity it's more easier to separate the init from
	 * oncreate
	 */
	public void init() {
		setContentView(R.layout.activity_contact);
		tvReqName = (TextView) findViewById(R.id.TVRequesterName);
		tvReqDesc = (TextView) findViewById(R.id.TVRequestDesc);
		tvoffName = (TextView) findViewById(R.id.TVOffererName);
		tvClaimMessage = (TextView) findViewById(R.id.TVClaimMessage);
		tvOffDesc = (TextView) findViewById(R.id.TVOfferDesc);
		bContReq = (Button) findViewById(R.id.BContactRequester);
		bContOffer = (Button) findViewById(R.id.BContactOfferer);
		bContOffer.setOnClickListener(this);
		bContReq.setOnClickListener(this);
	}

	/**
	 * retrieve date from server to be shown this uses contactAction to retrieve
	 * claim data from server
	 * 
	 */
	public void retrieveServerData() {
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
			if (x != null) {
				obj = new JSONObject(x);
				tvReqName.setText(obj.getString("requesterName"));
				tvReqDesc.setText(obj.getString("requestDesc"));
				tvoffName.setText(obj.getString("offererName"));
				tvOffDesc.setText(obj.getString("offerDesc"));
				tvClaimMessage.setText(obj.getString("claimMessage"));
				reqID = obj.getInt("requesterID");
				offID = obj.getInt("offererID");
			}
			if (req.getStatusCode() != 200) {
				UI.makeToast(getApplicationContext(),
						"couldn't connect to server", Toast.LENGTH_LONG);
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * on click listener for Contact view this handles all the buttons in this
	 * view.
	 */
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
		Util.sendUserMessage(getApplicationContext(), userID, message,
				contactURL);
	}

}
