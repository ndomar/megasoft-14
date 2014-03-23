package com.megasoft.tweet.activities;

import android.app.Activity;
import android.app.ActionBar;
import android.app.Fragment;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.os.Build;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.megasoft.tweet.R;
import com.megasoft.tweet.http.HttpGetRequest;
import com.megasoft.tweet.http.HttpPostRequest;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class TweetActivity extends Activity {


    private void clearStream() {
        LinearLayout tweets = (LinearLayout) findViewById(R.id.tweetsContainer);
        tweets.removeAllViews();
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_tweet);

        SharedPreferences config = getSharedPreferences("AppConfig", 0);
        final Intent intent = new Intent(this, LoginActivity.class);

        if(!config.contains("sessionId")){
           startActivity(intent);
        }
        final String sessionId = config.getString("sessionId", null);

        final EditText text   = (EditText) findViewById(R.id.text);
        Button tweet    = (Button) findViewById(R.id.tweet);
        Button refresh  = (Button) findViewById(R.id.refresh);
        final LinearLayout container = (LinearLayout) findViewById(R.id.tweetsContainer);
        final Activity self = this;


        tweet.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String myTweet = text.getText().toString();
                try {
                    JSONObject json = new JSONObject();
                    json.put("text", myTweet);
                    json.put("sessionId", sessionId);
                    (new HttpPostRequest(){

                        public void onPostExecute() {
                            Log.d("walla3", "tweet has been posted");
                        }

                    }).execute(json.toString(), "tweet/create");

                } catch (JSONException ex) {
                    ex.printStackTrace();
                }

            }
        });

        refresh.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                (new HttpGetRequest(){
                    protected void onPostExecute(String res) {
                        try {
                        clearStream();
                        JSONArray jsonArr = new JSONArray(res);
                        Log.e("walla3", res);
                        for (int i = 0; i < jsonArr.length(); i++) {
                            JSONObject obj = jsonArr.getJSONObject(i);
                            TextView textView = new TextView(self);
                            textView.setText(obj.getString("username") + ": " + obj.getString("content"));
                            container.addView(textView);
                        }

                        } catch(JSONException ex) {
                            ex.printStackTrace();
                        }

                    }
                }).execute("tweets/" + sessionId);
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.tweet, menu);
        return true;
    }

}
