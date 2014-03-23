package com.megasoft.tweet.activities;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.StrictMode;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import com.megasoft.tweet.R;
import com.megasoft.tweet.http.HttpPostRequest;

import org.json.JSONException;
import org.json.JSONObject;

public class LoginActivity extends Activity {

    private View rootView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);

        //redirect if logged in

        final Intent intent = new Intent(this, TweetActivity.class);
        SharedPreferences config = getSharedPreferences("AppConfig", 0);
        if(config.contains("sessionId")){
            startActivity(intent);
        }

        setContentView(R.layout.activity_login);


        final TextView label = (TextView) findViewById(R.id.myLabel);

        Button login = (Button) findViewById(R.id.loginButton);
        String username = ((EditText) findViewById(R.id.username)).getText().toString();
        String password = ((EditText) findViewById(R.id.password)).getText().toString();
        final JSONObject obj = new JSONObject();

        try {
            obj.put("username", username);
            obj.put("password", password);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        login.setOnClickListener(new View.OnClickListener(){

            public void onClick(View v) {
                HttpPostRequest req = new HttpPostRequest() {
                    @Override
                    protected void onPostExecute(String res) {

                        try {

                        JSONObject json = new JSONObject(res);
                        //store it on the disk
                        SharedPreferences settings = getSharedPreferences("AppConfig", 0);
                        SharedPreferences.Editor editor = settings.edit();
                        editor.putString("sessionId", json.getString("sessionId"));
                        editor.commit();
                        startActivity(intent);

                        } catch (JSONException ex) {
                          ex.printStackTrace();
                        }
                    }
                };

                req.execute(obj.toString(), "site/login");
            }
        });










    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {

        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
}