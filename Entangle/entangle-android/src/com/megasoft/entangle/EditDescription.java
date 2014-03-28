package com.example.editinfo;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.protocol.HTTP;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;

public class EditDescription extends Activity {

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getIntent();
		setContentView(R.layout.edit_description);

	}

	public void SaveNewDescription(View view) {

		EditText newDescription = (EditText) findViewById(R.id.NewDescription);
		String text = newDescription.getText().toString();
		int sessionId = 1515;
		HttpGet get = new HttpGet("http://entangle.io/user/whoAmI/" + sessionId);
		HttpClient client = new DefaultHttpClient();

		try {
			HttpResponse response = client.execute(get);

			if (response instanceof JSONObject) {
				//Getting the user from endPoint whoAmI
				HttpEntity entity = response.getEntity();
				InputStream instream = entity.getContent();
				String responseString = convertStreamToString(instream);
				JSONObject whoAmIResponse = new JSONObject(responseString);
				whoAmIResponse.put("description", text);
				
				HttpPut saveEdit = new HttpPut("http://entangle.io/user/EditDescription");
				StringEntity se = new StringEntity(whoAmIResponse.toString());
				se.setContentType(new BasicHeader(HTTP.CONTENT_TYPE,
						"application/json"));
				saveEdit.setEntity(se);
				response = client.execute(saveEdit);
				Intent editDes = new Intent(this, MyInfo.class);
				editDes.putExtra("Desc", text);
				startActivity(editDes);
			}

		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		} 


		// toBeUpdatedDescription.setText(newDescription.getText().toString());
	}

	private static String convertStreamToString(InputStream is) {

		BufferedReader reader = new BufferedReader(new InputStreamReader(is));
		StringBuilder sb = new StringBuilder();

		String line = null;
		try {
			while ((line = reader.readLine()) != null) {
				sb.append(line + "\n");
			}
		} catch (IOException e) {
			e.printStackTrace();
		} finally {
			try {
				is.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
		return sb.toString();
	}
}
